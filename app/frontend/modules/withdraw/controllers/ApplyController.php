<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/31 下午2:40
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\controllers;


use app\common\components\ApiController;
use app\common\events\withdraw\WithdrawAppliedEvent;
use app\common\events\withdraw\WithdrawApplyEvent;
use app\common\events\withdraw\WithdrawApplyingEvent;
use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\frontend\modules\withdraw\models\Withdraw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\frontend\modules\withdraw\services\StatisticalPresentationService;
use app\common\helpers\Url;
use Yunshop\Commission\models\Agents;

class ApplyController extends ApiController
{
    private $withdraw_set;

    /**
     * @var static
     */
    private $pay_way;

    /**
     * @var double
     */
    private $amount;

    /**
     * @var double
     */
    private $poundage;


    /**
     * @var array
     */
    private $withdraw_data;


    public function __construct()
    {
        parent::__construct();

        $this->withdraw_set = $this->getWithdrawSet();
    }

    //提现接口
    public function index()
    {
        $this->withdrawLimitation();
        list($amount, $pay_way, $poundage, $withdraw_data) = $this->getPostValue();
        $this->amount = $amount;
        $this->pay_way = $pay_way;
        $this->poundage = $poundage;
        $this->withdraw_data = $withdraw_data;
        //提现限额判断
        $this->cashLimitation();

        //插入提现
        $result = $this->withdrawStart();

        $set = \Setting::get('shop.lang.zh_cn.income');

        $name = '提现';
        if ($set['name_of_withdrawal']){
            $name = $set['name_of_withdrawal'];
        }
        if ($result === true) {
            return $this->successJson($name.'申请成功');
        }
        return $this->errorJson($result ?: $name.'申请失败');
    }

    private function cashLimitation(){
        $set = Setting::get('withdraw.income');

        //提交提现的次数
        $number_of_submissions = count($this->withdraw_data);

        if( $this->pay_way == 'wechat'){
            $wechat_frequency = floor($set['wechat_frequency'] ?: 10 );
            //统计用户今天提现的次数
            $statisticalPresentationService = new StatisticalPresentationService;
            $today_withdraw_count = $statisticalPresentationService->statisticalPresentation('wechat');

            if(($number_of_submissions + $today_withdraw_count) > $wechat_frequency  ){
                \Log::debug('提现到微信失败',['今天提现次数',$today_withdraw_count,'本次提现次数',$number_of_submissions,'每日限制次数',$wechat_frequency]);
                return $this->errorJson('提现失败,每日提现到微信次数不能超过'.$wechat_frequency.'次');
            }
        }elseif($this->pay_way == 'alipay'){
            $alipay_frequency = floor($set['alipay_frequency'] ?: 10);
            //统计用户今天提现的次数  + 供应商提现的次数
            $statisticalPresentationService = new StatisticalPresentationService;
            $today_withdraw_count = $statisticalPresentationService->statisticalPresentation('alipay');
            if(($number_of_submissions + $today_withdraw_count) > $alipay_frequency  ){
                \Log::debug('提现到支付宝失败',['今天提现次数',$today_withdraw_count,'本次提现次数',$number_of_submissions,'每日限制次数',$alipay_frequency]);
                return $this->errorJson('提现失败,每日提现到支付宝次数不能超过'.$alipay_frequency.'次');

            }
        }
    }

    private function withdrawLimitation()
    {
        if (app('plugins')->isEnabled('commission')) {
            $set = \Setting::get('plugin.commission');
            $agent = Agents::uniacid()->where('member_id', \YunShop::app()->getMemberId())->with('agentLevel')->first();
            if (!$agent->agent_level_id) {
                if ($set['no_withdraw']) {
                    return $this->errorJson('不满足分销商等级，不可提现',['status' => 0]);
                }
            } else {
                if ($agent->agentLevel->no_withdraw) {
                    return $this->errorJson('不满足分销商等级，不可提现',['status' => 0]);
                }
            }
        }
    }

    private function withdrawStart()
    {
        try{

            DB::transaction(function () {
                $this->_withdrawStart();
            });
            return true;

        } catch (\Exception $exception) {
            
            return $exception->getMessage();
        }
    }


    /**
     * @return bool
     * @throws AppException|
     */
    private function _withdrawStart()
    {
        $amount = '0';
        foreach ($this->withdraw_data as $key => $item) {

            $withdrawModel = new Withdraw();

            $withdrawModel->mark = $item['key_name'];
            $withdrawModel->withdraw_set = $this->withdraw_set;
            $withdrawModel->income_set = $this->getIncomeSet($item['key_name']);
            $withdrawModel->fill($this->getWithdrawData($item));

            event(new WithdrawApplyEvent($withdrawModel));
            
            $validator = $withdrawModel->validator();
            if ($validator->fails()) {
                throw new AppException("ERROR:Data anomaly -- {$item['key_name']}::{$validator->messages()->first()}");
            }

            event(new WithdrawApplyingEvent($withdrawModel));

            if (!$withdrawModel->save()) {
                throw new AppException("ERROR:Data storage exception -- {$item['key_name']}");
            }
            app('plugins')->isEnabled('converge_pay') && $this->withdraw_set['free_audit'] == 1 && $this->pay_way == 'converge_pay' ? \Setting::set('plugin.convergePay_set.notifyWithdrawUrl', Url::shopSchemeUrl('payment/convergepay/notifyUrlWithdraw.php')) : null;
            event(new WithdrawAppliedEvent($withdrawModel));

            $amount = bcadd($amount, $withdrawModel->amounts, 2);
        }
        if (bccomp($amount, $this->amount, 2) != 0) {
            throw new AppException('提现失败：提现金额错误');
        }
        return true;
    }


    /**
     * @param $withdraw_item
     * @return array
     * @throws AppException
     */
    private function getWithdrawData($withdraw_item)
    {
        //dd($withdraw_item);
        return [
            'withdraw_sn'       => Withdraw::createOrderSn('WS', 'withdraw_sn'),
            'uniacid'           => \YunShop::app()->uniacid,
            'member_id'         => $this->getMemberId(),
            'type'              => $withdraw_item['type'],
            'type_name'         => $withdraw_item['type_name'],
            'type_id'           => $withdraw_item['type_id'],
            'amounts'           => $withdraw_item['income'],
            'poundage'          => '0.00',
            'poundage_rate'     => '0.00',
            'poundage_type'     => $withdraw_item['poundage_type']?:0,
            'actual_poundage'   => '0.00',
            'actual_amounts'    => '0.00',
            'servicetax'        => '0.00',
            'servicetax_rate'   => '0.00',
            'actual_servicetax' => '0.00',
            'pay_way'           => $this->pay_way,
            'manual_type'       => !empty($this->withdraw_set['manual_type']) ? $this->withdraw_set['manual_type'] : 1,
            'status'            => Withdraw::STATUS_INITIAL,
            'audit_at'          => null,
            'pay_at'            => null,
            'arrival_at'        => null,
            'created_at'        => time(),
            'updated_at'        => time(),
        ];
    }


    /**
     * 提现对应收入设置
     *
     * @param $mark
     * @return array
     */
    private function getIncomeSet($mark)
    {
        return Setting::get('withdraw.' . $mark);
    }


    /**
     * 提现设置
     *
     * @return array
     */
    private function getWithdrawSet()
    {
        return Setting::get('withdraw.income');
    }


    /**
     * @return array
     * @throws AppException
     */
    private function getPostValue()
    {
        $post_data = \YunShop::request()->data;
        Log::info('收入提现提交数据：', [$post_data]);
        //$post_data = $this->testData();

        !is_array($post_data) && $post_data = json_decode($post_data, true);

        if (!$post_data) {
            throw new AppException('Undetected submission of data');
        }
        // 12月20号修改 提现原代码是提现金额不能小于1元
        if ($post_data['total']['amounts'] < 0) {
            throw new AppException('提现金额不能小于0元');
        }

        $amount = $post_data['total']['amounts'];
        $pay_way = $post_data['total']['pay_way'];
        $poundage = $post_data['total']['poundage'];
        $withdraw_data = $post_data['withdrawal'];

        return [$amount, $pay_way, $poundage, $withdraw_data];
    }


    /**
     * @return int
     * @throws AppException
     */
    private function getMemberId()
    {
        $member_id = \YunShop::app()->getMemberId();

        if (!$member_id) {
            throw new AppException('Please log in');
        }
        return $member_id;
    }


    private function testData()
    {
        $data = [
            'total' => [
                'amounts' => 1816.01,
                'poundage' => 181.6,
                'pay_way' => 'balance',
            ],
            'withdrawal' => [
                [
                    'type' => 'Yunshop\ConsumeReturn\common\models\Log',
                    'key_name' => 'consumeReturn',
                    'type_name' => '消费返现',
                    'type_id' => '7223,7319,7408,7477,7605,7680,7808,7881,7973,8048,8137,8205,8274,8401,8535,8670,8721,8805,8877,9030,9145,9237,9325,9403,9477,9554,9755,9837,9919,10012,10101,10184,10374,10528,10650,10760,10858',
                    'income' => '12032.92',
                    'poundage' => '12.03',
                    'poundage_rate' => '0.1',
                    'servicetax' => '1202.08',
                    'servicetax_rate' => '10',
                    'can' => '1',
                    'roll_out_limit' => '0',
                    'selected' => 1,
                ],
                [
                    'type' => 'Yunshop\LevelReturn\models\LevelReturnModel',
                    'key_name' => 'levelReturn',
                    'type_name' => '等级返现',
                    'type_id' => '7426,7481,7556,7883,7884,7885,7886,8222,8223,8224,8281,8360,8552,8895,8954,8955,8956,8957,9107,9621,10598,10599,10784,10785,10786,10989',
                    'income' => '20241.59',
                    'poundage' => '20.24',
                    'poundage_rate' => '0.1',
                    'servicetax' => '2022.13',
                    'servicetax_rate' => '10',
                    'can' => '1',
                    'roll_out_limit' => '10',
                    'selected' => 1,
                ]
            ]
        ];
        return $data;
    }

}
