<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/28 下午3:49
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;


use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\BalanceNoticeService;
use app\frontend\models\Member;
use app\frontend\models\MemberShopInfo;
use app\frontend\modules\finance\models\Withdraw;
use app\frontend\modules\finance\models\WithdrawSetLog;
use app\frontend\modules\finance\services\WithdrawManualService;
use app\frontend\modules\withdraw\services\WithdrawMessageService;
use Illuminate\Support\Facades\DB;
use app\common\events\withdraw\WithdrawBalanceAppliedEvent;
use app\common\helpers\Url;
use app\frontend\modules\withdraw\services\StatisticalPresentationService;

class BalanceWithdrawController extends BalanceController
{

    public $withdrawModel;

    public $memberModel;


    public function __construct()
    {
        parent::__construct();
        $this->memberModel = $this->getMemberModel();
    }

    /**
     * 余额提现页面按钮接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function page()
    {
        $data = [
            'balance'           => $this->memberModel->credit2 ?: 0,
            'wechat'            => $this->balanceSet->withdrawWechat(),
            'alipay'            => $this->balanceSet->withdrawAlipay(),
            'manual'            => $this->balanceSet->withdrawManual(),
            'huanxun'           => $this->balanceSet->withdrawHuanxun(),
            'eup_pay'           => $this->balanceSet->withdrawEup(),
            'converge_pay'      => $this->balanceSet->withdrawConverge(),
            'withdraw_multiple' => $this->balanceSet->withdrawMultiple(),
            'poundage'          => $this->getPagePoundage(),
        ];

        return $this->successJson('获取数据成功', $data);
    }


    public function withdraw()
    {
        if (!$this->balanceSet->withdrawSet()) {
            return $this->errorJson('未开启余额提现');
        }

        return $this->withdrawStart();
    }


    public function isCanSubmit()
    {
        if ($this->balanceSet->withdrawManual()) {
            return $this->successJson('ok', $this->manualIsCanSubmit());
        }
        return $this->errorJson('未开启余额手动提现');
    }


    private function getPagePoundage()
    {
        $withdraw_poundage = $this->balanceSet->withdrawPoundage();
        if (empty($withdraw_poundage)) {
            return '';
        }
        $poundage = '手续费比例：' . $this->balanceSet->withdrawPoundage() . '%';
        if ($this->balanceSet->withdrawPoundageType() == 1) {
            $poundage = '固定手续费：' . $this->balanceSet->withdrawPoundage() . '元';
        }

        $poundage_full_cut = $this->balanceSet->withdrawPoundageFullCut();
        if (!empty($poundage_full_cut)) {
            $poundage = $poundage . "，提现金额满" . $poundage_full_cut . "元减免手续费";
        }
        return $poundage;
    }


    private function withdrawStart()
    {
        $withdrawType = $this->getWithdrawType();
        if ($withdrawType == 'wechat' && !$this->balanceSet->withdrawWechat()) {
            return $this->errorJson('未开启提现到微信');
        }
        if ($withdrawType == 'alipay' && !$this->balanceSet->withdrawAlipay()) {
            return $this->errorJson('未开启提现到支付宝');
        }
        if ($withdrawType == 'alipay' && !$this->getMemberAlipaySet()) {
            return $this->errorJson('您未配置支付宝信息，请先修改个人信息中支付宝信息');
        }
        if ($withdrawType == 'manual' && !$this->balanceSet->withdrawManual()) {
            return $this->errorJson('未开启余额手动提现');
        }
        if ($withdrawType == 'eup_pay' && !$this->balanceSet->withdrawEup()) {
            return $this->errorJson('未开启余额EUP提现');
        }
        if ($withdrawType == 'converge_pay' && !$this->balanceSet->withdrawConverge()) {
            return $this->errorJson('未开启余额汇聚提现');
        }

        $manual_result = $this->manualIsCanSubmit();
        if ($withdrawType == 'manual' && !$manual_result['status']) {
            return $this->errorJson('需要完善信息', $manual_result);
        }


        $withdrawFetter = $this->balanceSet->withdrawAstrict();
        if ($withdrawFetter > $withdrawMoney = $this->getWithdrawMoney()) {
            return $this->errorJson('提现金额不能小于' . $withdrawFetter . '元');
        }
        $multiple = $this->balanceSet->withdrawMultiple();
        if ($multiple && fmod($withdrawMoney, $multiple) != 0) {
            throw new AppException('提现值必须是' . $multiple . '的倍数');
        }

        if (bcsub($this->getWithdrawMoney(), $this->getPoundage(), 2) < 1) {
            return $this->errorJson('扣除手续费后的金额不能小于1元');
        }

        $this->cashLimitation();

        DB::beginTransaction();

        //写入提现记录
        $this->withdrawModel = new Withdraw();

        $this->withdrawModel->fill($this->getWithdrawData());
        $validator = $this->withdrawModel->validator();
        if ($validator->fails()) {
            return $this->errorJson($validator->messages()->first());
        }
        if (!$this->withdrawModel->save()) {
            return $this->errorJson('提现失败，记录写入失败');
        }


        //写入提现关联表，当前设置记录
        $withdrawSetLog = new WithdrawSetLog();

        $withdrawSetLog->fill($this->getWithdrawSetLogData());
        $validator = $withdrawSetLog->validator();
        if ($validator->fails()) {
            return $this->errorJson($validator->messages()->first());
        }
        if (!$withdrawSetLog->save()) {
            return $this->errorJson('提现失败，记录Log写入失败');
        }


        //修改会员余额
        $result = (new BalanceChange())->withdrawal($this->getBalanceChangeData());
        if ($result === true) {
            DB::commit();
            app('plugins')->isEnabled('converge_pay') && Setting::get('withdraw.balance.audit_free') == 1 && $withdrawType == 'converge_pay' ? \Setting::set('plugin.convergePay_set.notifyWithdrawUrl', Url::shopSchemeUrl('payment/convergepay/notifyUrlWithdraw.php')) : null;
            event(new WithdrawBalanceAppliedEvent($this->withdrawModel));
            BalanceNoticeService::withdrawSubmitNotice($this->withdrawModel);
            //提现通知管理员
            (new WithdrawMessageService())->withdraw($this->withdrawModel);
            return $this->successJson('提现申请成功');

        }


        DB::rollBack();
        return $this->errorJson('提现写入失败，请联系管理员');
    }

    //提现限制
    private function cashLimitation()
    {
        $start = strtotime(date("Y-m-d"), time());
        $end = $start + 60 * 60 * 24;
        $withdrawType = $this->getWithdrawType();
        //提现金额
        $amount = $this->getWithdrawMoney();
        if ($withdrawType == 'wechat') {
            //微信提现限制设置
            $set = $this->balanceSet->withdrawWechatLimit();

            $wechat_min = $set['wechat_min'];
            $wechat_max = $set['wechat_max'];
            $wechat_frequency = floor($set['wechat_frequency'] ?: 10);

            //统计用户今天提现的次数
            $statisticalPresentationService = new StatisticalPresentationService;
            $today_withdraw_count = $statisticalPresentationService->statisticalPresentation('wechat') + 1;
            if ($today_withdraw_count <= $wechat_frequency) {
                if ($amount < $wechat_min && !empty($wechat_min)) {
                    throw new AppException("提现到微信单笔提现额度最低{$wechat_min}元");
                } elseif ($amount > $wechat_max && !empty($wechat_max)) {
                    throw new AppException("提现到微信单笔提现额度最高{$wechat_max}元");
                }
            } else {
                return $this->errorJson('提现失败,每日提现到微信次数不能超过' . $wechat_frequency . '次');
            }
        } elseif ($withdrawType == 'alipay') {
            $set = $this->balanceSet->withdrawAlipayLimit();
            $alipay_min = $set['alipay_min'];
            $alipay_max = $set['alipay_max'];
            $alipay_frequency = floor($set['alipay_frequency'] ?: 10);

            //统计用户今天提现的次数
            $statisticalPresentationService = new StatisticalPresentationService;
            $today_withdraw_count = $statisticalPresentationService->statisticalPresentation('alipay') + 1;
            if ($today_withdraw_count <= $alipay_frequency) {
                if ($amount < $alipay_min && !empty($alipay_min)) {
                    throw new AppException("提现到支付宝单笔提现额度最低{$alipay_min}元");
                } elseif ($amount > $alipay_max && !empty($alipay_max)) {
                    throw new AppException("提现到支付宝单笔提现额度最高{$alipay_max}元");
                }
            } else {
                return $this->errorJson('提现失败,每日提现到支付宝次数不能超过' . $alipay_frequency . '次');
            }

        }
    }

    /**
     * @return array
     */
    private function getWithdrawData()
    {
        $dalance = Setting::get('shop.shop');
        $dalance['credit'] = empty($dalance['credit']) ? "余额" : $dalance['credit'];
        return array(
            'withdraw_sn'     => Withdraw::createOrderSn('WS', 'withdraw_sn'),
            'uniacid'         => $this->uniacid,
            'member_id'       => $this->memberModel->uid,
            'type'            => 'balance',
            'type_id'         => '',
            'type_name'       => $dalance['credit'],//'余额提现'
            'amounts'         => $this->getWithdrawMoney(),                   //提现金额
            'poundage'        => $this->getPoundage(),                        //提现手续费
            'poundage_rate'   => $this->balanceSet->withdrawPoundageType() ? '0' : $this->PoundageRate(),//手续费比例
            'pay_way'         => $this->getWithdrawType(),                    //打款方式
            'status'          => '0',                                         //0未审核，1未打款，2已打款， -1无效
            'actual_amounts'  => bcsub($this->getWithdrawMoney(), $this->getPoundage(), 2),
            'actual_poundage' => $this->getPoundage(),
            'manual_type'     => Setting::get('withdraw.balance')['balance_manual_type'] ?: 1,
        );
    }


    /**
     * @return array
     */
    private function getWithdrawSetLogData()
    {
        return [
            'withdraw_id'       => $this->withdrawModel->id,
            'poundage_type'     => $this->balanceSet->withdrawPoundageType(),
            'poundage'          => $this->balanceSet->withdrawPoundage(),
            'poundage_full_cut' => $this->balanceSet->withdrawPoundageFullCut(),
            'withdraw_fetter'   => $this->balanceSet->withdrawAstrict(),
            'remark'            => '',
            'created_at'        => time()
        ];
    }


    /**
     * 获取余额提现改变余额 data 数据
     * @return array
     */
    private function getBalanceChangeData()
    {
        return array(
            'member_id'    => \YunShop::app()->getMemberId(),
            'remark'       => '会员余额提现' . $this->withdrawModel->amounts,
            'source'       => ConstService::SOURCE_WITHDRAWAL,
            'relation'     => $this->withdrawModel->withdraw_sn,
            'operator'     => ConstService::OPERATOR_MEMBER,
            'operator_id'  => $this->withdrawModel->member_id,
            'change_value' => $this->withdrawModel->amounts
        );
    }


    /**
     * 当前提现金额需支付手续费值
     * @return string
     */
    private function getPoundage()
    {
        if (!$this->isHasPoundage()) {
            return '0';
        } elseif ($this->balanceSet->withdrawPoundageType() == 1) {
            return $this->balanceSet->withdrawPoundage();
        } else {
            return bcdiv(bcmul($this->getWithdrawMoney(), $this->balanceSet->withdrawPoundage(), 4), 100, 2);
        }
    }


    /**
     * 手续费比例设置值，可以是固定金额，也可以是比例，需要通过 poundage_type 判断
     * @return string
     */
    private function PoundageRate()
    {
        return $this->isHasPoundage() ? $this->balanceSet->withdrawPoundage() : '0';
    }


    /**
     * 增加 提现金额 满N元 减免手续费，true 正常计算手续费，false 减免手续费 YITIAN::2017-09-28
     * @return bool
     */
    private function isHasPoundage()
    {
        $poundage_full_cut = $this->balanceSet->withdrawPoundageFullCut();
        if (!empty($poundage_full_cut)) {
            return bccomp($this->getWithdrawMoney(), $poundage_full_cut, 2) != -1 ? false : true;
        }
        return true;
    }


    private function getWithdrawMoney()
    {
        $withdraw_money = trim(\YunShop::request()->withdraw_money);
        if ($withdraw_money) {
            return $withdraw_money;
        }
        throw new AppException('未获取到提现金额');
    }


    private function getWithdrawType()
    {
        $withdraw_type = trim(\YunShop::request()->withdraw_type);
        switch ($withdraw_type) {
            case 1:
                return 'wechat';
                break;
            case 2:
                return 'alipay';
                break;
            case 3:
                return 'manual';
                break;
            case 4:
                return 'eup_pay';
                break;
            case 5:
                return 'huanxun';
                break;
            case 6:
                return 'converge_pay';
                break;
            default:
                throw new AppException('未找到提现类型');
                break;
        }
    }

    private function manualIsCanSubmit()
    {
        $manual_type = Setting::get('withdraw.balance')['balance_manual_type'] ?: 1;

        switch ($manual_type) {
            case 2:
                $result['manual_type'] = 'wechat';
                $result['status'] = WithdrawManualService::getWeChatStatus();
                break;
            case 3:
                $result['manual_type'] = 'alipay';
                $result['status'] = WithdrawManualService::getAlipayStatus();
                break;
            default:
                $result['manual_type'] = 'bank';
                $result['status'] = WithdrawManualService::getBankStatus();
        }
        return $result;
    }




//***********************************  以下方法可以在member model 中实现  ***********************************************//


    /**
     * 获取会员 支付宝 设置，
     * @return bool
     */
    private function getMemberAlipaySet()
    {
        $array = MemberShopInfo::select('alipay', 'alipayname')->where('member_id', \YunShop::app()->getMemberId())->first();
        if ($array && $array['alipay'] && $array['alipayname']) {
            return true;
        }
        return false;
    }

    /**
     * 获取验证登录会员是否存在， 因支付宝不需要验证，暂时从 BalanceController 中提出来，
     * @return mixed
     * @throws AppException
     */
    private function getMemberModel()
    {
        $memberModel = Member::where('uid', \YunShop::app()->getMemberId())->first();
        if ($memberModel) {
            return $memberModel;
        }
        throw new AppException('未获取到会员信息');
    }

    public function convergeWithdraw()
    {
        $data['cost_money'] = number_format($this->getWithdrawMoney(), 2);
        $data['actual_amount'] = bcsub($this->getWithdrawMoney(), $this->getPoundage(), 2);
        $data['poundage'] = number_format($this->getPoundage(), 2);

        return $this->successJson('获取数据成功', $data);
    }
}
