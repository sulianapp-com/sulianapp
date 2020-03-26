<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/8 上午9:19
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\backend\modules\finance\services\WithdrawService as Withdraw;
use app\common\services\credit\ConstService;
use app\common\services\PayFactory;

class IncomeFreeAuditService
{
    private $amount;

    public function incomeFreeAudit($withdraw,$payWay)
    {
        $result = false;
        $remark = '提现打款-' . $withdraw->type_name . '-金额:' . $withdraw->actual_amounts . '元,';

        if ($payWay == 'balance') {
            $result = $this->balanceWithdrawPay($withdraw, $remark);
            \Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "打款到余额中!");
        }
        if ($payWay == 'wechat') {
            $result = $this->wechatWithdrawPay($withdraw, $remark);
            \Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "微信打款中!");
        }

        if ($result) {
            return Withdraw::otherWithdrawSuccess($withdraw->id);
        }
        return false;
    }


    private function balanceWithdrawPay($withdraw,$remark)
    {
        $data = array(
            'member_id'     => $withdraw->member_id,
            'remark'        => $remark,
            'source'        => ConstService::SOURCE_INCOME,
            'relation'      => '',
            'operator'      => ConstService::OPERATOR_MEMBER,
            'operator_id'   => $withdraw->id,
            'change_value'  => $withdraw->actual_amounts
        );
        return (new BalanceChange())->income($data);
    }

    private function wechatWithdrawPay($withdraw,$remark)
    {
        return  PayFactory::create(1)->doWithdraw($withdraw->member_id, $withdraw->withdraw_sn, $withdraw->actual_amounts, $remark);
    }




}
