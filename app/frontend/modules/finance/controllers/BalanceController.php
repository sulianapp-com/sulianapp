<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\finance\controllers;

use app\common\exceptions\AppException;
use app\common\models\MemberShopInfo;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\BalanceNoticeService;
use app\common\events\payment\RechargeComplatedEvent;
use app\common\services\PayFactory;
use app\common\components\ApiController;

use app\frontend\modules\finance\models\Balance as BalanceCommon;
use app\frontend\modules\finance\models\BalanceTransfer;
use app\frontend\modules\finance\models\BalanceConvertLove;
use app\frontend\modules\finance\models\Withdraw;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\frontend\modules\finance\services\BalanceService;

use app\backend\modules\member\models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceController extends ApiController
{

    public $memberModel;

    /**
     * @var BalanceService
     */
    public $balanceSet;

    public $uniacid;


    public function preAction()
    {
        parent::preAction();
        $this->balanceSet = new BalanceService();
        //$this->memberModel = $this->getMemberModel();
        $this->uniacid = \YunShop::app()->uniacid;
    }



    /**
     * Get an instance of the login member model
     * todo 会员 model 实例应该在 ApiController 中实现会员对象 YITIAN::2017-09-27
     *
     * todo 余额支付宝充值 不能验证会员登录，导致目前不能正常使用，应该将 alipay 方法提出
     * @return mixed
     * @throws AppException
     */
    private function getMemberModel()
    {
        $memberModel = Member::where('uid',\YunShop::app()->getMemberId())->first();
        if ($memberModel) {
            return $memberModel;
        }
        throw new AppException('未获取到会员信息');
    }


    public function memberBalance()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $result['credit2'] = $memberInfo->credit2;
            return $this->successJson('获取数据成功', $result);
        }
        return $this->errorJson('未获取到会员数据');
    }



    //todo 余额 controller 重构 YiTian::2017-09-27


    protected $publicAction = ['alipay'];
    protected $ignoreAction = ['alipay'];

    private $memberInfo;

    private $model;

    private $money;


    /**
     * 会员余额页面信息，（余额设置+会员余额值）
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $result = (new BalanceService())->getBalanceSet();
            $result['credit2'] = $memberInfo->credit2;
            $result['buttons'] = $this->getPayTypeButtons();
            $result['typename'] = '充值';
            $result['love_name'] =  (app('plugins')->isEnabled('designer') == 1) ? LOVE_NAME  : '爱心值';
            $result['convert'] = (new BalanceService())->convertSet();
            return $this->successJson('获取数据成功', $result);
        }
        return $this->errorJson('未获取到会员数据');

    }

    /**
     * 获取充值按钮
     *
     * @return array
     */
    private function getPayTypeButtons()
    {
        $event = new RechargeComplatedEvent([]);
        event($event);
        $result = $event->getData();
        $type = \YunShop::request()->type;
        if ($type == 2) {
            $button = [];
            foreach ($result as $item) {
                if ($item['value'] == 1 || $item['value'] == 28 || $item['value'] == 33) {
                    $button[] = $item;
                }
            }
            return $button;
        }
        //头条小程序
        if ($type == 11) {
            $button = [];
            foreach ($result as $item) {
                if ($item['value'] == 51 || $item['value'] == 52) {
                    $button[] = $item;
                }
            }
            return $button;
        } else {
            foreach ($result as $key=>$item) {
                if ($item['value'] == 51 || $item['value'] == 52) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }


    /**
     * 会员余额转化爱心值
     * @return \Illuminate\Http\JsonResponse
     */
    public function conver()
    {
        if (!$this->balanceSet->convertSet()) {
             return $this->errorJson('未开启余额转化');
        }
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $result = (new BalanceService())->getBalanceSet();
            $result['credit2'] = $memberInfo->credit2;
            $result['rate'] = $this->balanceSet->convertRate();
            return $this->successJson('获取数据成功', $result);
        }
        return $this->errorJson('未获取到会员数据');

    }



    //余额充值+充值优惠
    public function recharge()
    {
        $result = (new BalanceService())->rechargeSet() ? $this->rechargeStart() : '未开启余额充值';
        if ($result === true) {
            $type = intval(\YunShop::request()->pay_type);
            if ($type == PayFactory::PAY_WEACHAT
                || $type == PayFactory::PAY_YUN_WEACHAT
                || $type == PayFactory::PAY_Huanxun_Quick
                || $type == PayFactory::PAY_Huanxun_Wx
                || $type == PayFactory::WFT_PAY
                || $type == PayFactory::WFT_ALIPAY
                || $type == PayFactory::PAY_WECHAT_HJ
                || $type == PayFactory::PAY_ALIPAY_HJ
                || $type == PayFactory::PAY_WECHAT_JUEQI
            ) {
                return $this->successJson('支付接口对接成功', array_merge(['ordersn' => $this->model->ordersn], $this->payOrder()));
            }
            //头条支付
            if ($type == PayFactory::PAY_WECHAT_TOUTIAO
                || $type == PayFactory::PAY_ALIPAY_TOUTIAO) {
                $data['ordersn'] = $this->model->ordersn;
                $data['orderInfo'] = $this->payOrder();
                return $this->successJson('支付接口对接成功', $data);
            }

            //app支付宝支付添加新支付配置
            if ($type == PayFactory::PAY_APP_ALIPAY) {
                $isnewalipay = \Setting::get('shop_app.pay.newalipay');
                return $this->successJson('支付接口对接成功', ['ordersn' => $this->model->ordersn, 'isnewalipay' => $isnewalipay]);
            } else {
                return $this->successJson('支付接口对接成功', ['ordersn' => $this->model->ordersn]);
            }
        }
        //app支付宝新旧版值

        return $this->errorJson($result);
    }

    //余额充值，如果是支付宝支付需要二次请求 alipay 支付接口
    public function alipay()
    {
        $orderSn = \YunShop::request()->order_sn;

        $this->model = BalanceRecharge::ofOrderSn($orderSn)->withoutGlobalScope('member_id')->first();
        if ($this->model) {
            return $this->successJson('支付接口对接成功', $this->payOrder());
        }

        return $this->errorJson('充值订单不存在');
    }

    public function cloudWechatPay()
    {
        $orderSn = \YunShop::request()->ordersn;

        $this->model = BalanceRecharge::ofOrderSn($orderSn)->withoutGlobalScope('member_id')->first();
        if ($this->model) {
            return $this->successJson('支付接口对接成功', $this->payOrder());
        }

        return $this->errorJson('充值订单不存在');
    }

    public function wechatPayJueqi()
    {
        $orderSn = \YunShop::request()->order_pay_id;

        $this->model = BalanceRecharge::ofOrderSn($orderSn)->withoutGlobalScope('member_id')->first();
        if ($this->model) {
            return $this->successJson('支付接口对接成功', $this->payOrder());
        }

        return $this->errorJson('充值订单不存在');
    }

    //余额转让
    public function transfer()
    {
        $result = (new BalanceService())->transferSet() ? $this->transferStart() : '未开启余额转让';

        return $result === true ? $this->successJson('转让成功') : $this->errorJson($result);
    }

    //余额转化爱心值
    public function convertLoveValue()
    {
        $result = (new BalanceService())->convertSet() ? $this->convertStart() : '未开启余额转化';
        return $result === true ? $this->successJson('转化成功') : $this->errorJson($result);
    }

    //记录【全部、收入、支出】
    public function record()
    {
        $memberInfo = $this->getMemberInfo();
        if ($memberInfo) {
            $type = \YunShop::request()->record_type;
            $recordList = BalanceCommon::getMemberDetailRecord($this->memberInfo->uid, $type);

            return $this->successJson('获取记录成功', $recordList->toArray());
        }
        return $this->errorJson('未获取到会员信息');
    }

    //获取会员信息
    private function getMemberInfo()
    {
        $member_id = \YunShop::app()->getMemberId();
        //$member_id = \YunShop::app()->getMemberId() ?: \YunShop::request()->uid;
        return $this->memberInfo = Member::getMemberInfoById($member_id) ?: false;
    }

    //充值开始
    private function rechargeStart()
    {
        if (!$this->getMemberInfo()) {
            return '未获取到会员数据,请重试！';
        }
        $this->model = new BalanceRecharge();
        $this->model->fill($this->getRechargeData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($this->model->save()) {
            return true;
        }
        return '充值写入失败，请联系管理员';
    }

    //余额转让开始
    private function transferStart()
    {
        $recipient = \YunShop::request()->recipient;
        if (!$this->getMemberInfo()) {
            return '未获取到会员信息';
        }
        if (!Member::getMemberInfoById(\YunShop::request()->recipient)) {
            return '被转让者不存在';
        }
        if ($this->memberInfo->uid == $recipient) {
            return '转让者不能是自己';
        }
        if (\YunShop::request()->transfer_money <= 0){
            return '转让金额必须大于零';
        }
        if ($this->memberInfo->credit2 < \YunShop::request()->transfer_money) {
            return '转让余额不能大于您的余额';
        }
        $this->model = new BalanceTransfer();

        $this->model->fill($this->getTransferData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        if ($this->model->save()) {
            //$result = (new BalanceService())->balanceChange($this->getChangeBalanceDataToTransfer());
            $result = (new BalanceChange())->transfer($this->getChangeBalanceDataToTransfer());
            if ($result === true) {
                $this->model->status = BalanceTransfer::TRANSFER_STATUS_SUCCES;
                if ($this->model->save()) {
                    return true;
                }
            }
            return '修改转让状态失败';
        }
        return '转让写入出错，请联系管理员';
    }

    //余额转换爱心值
    public function convertStart()
    {
        if (!class_exists('\Yunshop\Love\Common\Services\LoveChangeService')) {
            return $this->errorJson('未开启爱心值插件');
        }
        if (!$this->getMemberInfo()) {
            return '未获取到会员信息';
        }
        if (\YunShop::request()->convert_amount <= 0) {
            return '转化金额必须大于零';
        }
        if ($this->memberInfo->credit2 < \Yunshop::request()->convert_amount) {
            return '转化余额不能大于您的余额';
        }
        $this->model = new BalanceConvertLove();
        $this->model->fill($this->getConvertData());
        $validator = $this->model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }

        if ($this->model->save()) {
            //$result = (new BalanceService())->balanceChange($this->getChangeBalanceDataToTransfer());
            $result = (new BalanceChange())->convert($this->getChangeConverData());
            if ($result === true) {
                if ($this->awardMemberLove() !== true) {
                    (new BalanceChange())->convertCancel($this->getConvertCancel());  //爱心值交易失败，回滚余额
                    $this->errorJson('转化失败');
                }
                $this->model->status = BalanceConvertLove::CONVERT_STATUS_SUCCES;
                if ($this->model->save()) {
                    return true;
                }
            }
            return '修改转化状态失败';
        }
        return '转化写入出错，请联系管理员';
    }

    private function getConvertData()
    {
        return array(
            'uniacid' => \Yunshop::app()->uniacid,
            'member_id' => \Yunshop::app()->getMemberId(),
            'covert_amount' => \Yunshop::request()->convert_amount,
            'status' => BalanceConvertLove::CONVERT_STATUS_ERROR,
            'order_sn' => $this->getTransferOrderSN(),
            'remark' => '余额转化爱心值',
        );
    }

    private function getChangeConverData()
    {
        return array(
            'member_id' => $this->model->member_id,
            'remark' => '会员【ID:' . $this->model->member_id . '】余额转化爱心值会员【ID：' . $this->model->member_id . '】' . $this->model->covert_amount . '元',
            'source' => ConstService::SOURCE_CONVERT,
            'relation' => $this->model->order_sn,
            'operator' => ConstService::OPERATOR_MEMBER,
            'operator_id' => $this->model->member_id,
            'change_value' => $this->model->covert_amount,
        );
    }

    private function getConvertCancel()
    {
        return array(
            'member_id' => $this->model->member_id,
            'remark' => '会员【ID:' . $this->model->member_id . '】余额转化失败【ID：' . $this->model->member_id . '】' . $this->model->covert_amount . '元',
            'source' => ConstService::SOURCE_CONVERT_CANCEL,
            'relation' => $this->getTransferOrderSN(),
            'operator' => ConstService::OPERATOR_MEMBER,
            'operator_id' => $this->model->member_id,
            'change_value' => $this->model->covert_amount,
        );
    }

    /**
     * 转化爱心值
     * @return bool
     */
    private function awardMemberLove()
    {
        //统一走爱心值交易类型接口
        $_LoveChangeService = new  \Yunshop\Love\Common\Services\LoveChangeService('usable');
        $data = [
            'member_id' => $this->model->member_id,
            'change_value' => $this->calculateLoveValue(),
            'operator' => ConstService::OPERATOR_MEMBER,
            'operator_id' => $this->model->member_id,
            'remark' => '会员【ID:' . $this->model->member_id . '】余额转化爱心值会员【ID：' . $this->model->member_id . '】' . $this->model->covert_amount . '元',
            'relation' => $this->model->order_sn
        ];

        $result = $_LoveChangeService->conver($data);
        if ($result !== true) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }

    /**
     * 计算爱心值
     * @return string
     */
    private function calculateLoveValue()
    {
        return bcdiv(bcmul($this->model->covert_amount ,$this->balanceSet->convertRate(),2),100,2);
    }
    
    //余额转让详细记录数据
    private function getChangeBalanceDataToTransfer()
    {
        return array(
            'member_id'     =>  $this->model->transferor,
            'remark'        => '会员【ID:'.$this->model->transferor.'】余额转让会员【ID：'.$this->model->recipient. '】' . $this->model->money . '元',
            'source'        => ConstService::SOURCE_TRANSFER,
            'relation'      => $this->model->order_sn,
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $this->model->transferor,
            'change_value'  => $this->model->money,
            'recipient'     => $this->model->recipient,
        );
    }


    private function getTransferData()
    {
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'transferor'    => \YunShop::app()->getMemberId(),
            'recipient'     => \YunShop::request()->recipient,
            'money'         => trim(\YunShop::request()->transfer_money),
            'status'        => BalanceTransfer::TRANSFER_STATUS_ERROR,
            'order_sn'      => $this->getTransferOrderSN()
        );
    }


    /**
     * 生成唯一转让订单号
     * @return string
     */
    private function getTransferOrderSN()
    {
        $orderSn = createNo('TS', true);
        while (1) {
            if (!BalanceTransfer::ofOrderSn($orderSn)->first()) {
                break;
            }
            $orderSn = createNo('TS', true);
        }
        return $orderSn;
    }



    //充值记录表data数据
    private function getRechargeData()
    {
        //$change_money = substr(\YunShop::request()->recharge_money, 0, strpos(\YunShop::request()->recharge_money, '.')+3);
        $change_money = \YunShop::request()->recharge_money;
        return array(
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->memberInfo->uid,
            'old_money' => $this->memberInfo->credit2 ?: 0,
            'money' => floatval($change_money),
            'new_money' => $change_money + $this->memberInfo->credit2,
            'ordersn' => BalanceRecharge::createOrderSn('RV','ordersn'),
            'type' => intval(\YunShop::request()->pay_type),
            'status' => BalanceRecharge::PAY_STATUS_ERROR,
            'remark' => '会员前端充值'
        );
    }


    /**
     * 会员余额充值支付接口
     *
     * @return \app\common\services\strin5|array|bool|mixed|string
     * @throws AppException
     */
    private function payOrder()
    {
        $pay = PayFactory::create($this->model->type);


        $result = $pay->doPay($this->payData());
        \Log::info('++++++++++++++++++', $result);
        if ($this->model->type == 1) {
            $result['js'] = json_decode($result['js'], 1);
        }
        \Log::debug('余额充值 result', $result);
        return $result;
    }

    /**
     * 支付请求数据
     *
     * @return array
     * @Author yitian
     */
    private function payData()
    {
        $array = array(
            'subject' => '会员充值',
            'body' => '会员充值金额' . $this->model->money . '元:'. \YunShop::app()->uniacid,
            'amount' => $this->model->money,
            'order_no' => $this->model->ordersn,
            'extra' => ['type' => 2],
            'ask_for'=> 'recharge'
        );
        if ($this->model->type == PayFactory::PAY_CLOUD_ALIPAY) {
            $array['extra'] = ['type' => 2, 'pay' => 'cloud_alipay'];
        }

        if ($this->model->type == PayFactory::PAY_Huanxun_Quick) {
            $array['extra'] = ['type' => 2, 'pay' => 'quick'];
        }
        
        return $array;
    }

}

