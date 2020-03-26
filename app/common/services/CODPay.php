<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/24
 * Time: 下午12:42
 */

namespace app\common\services;

use app\backend\modules\member\models\MemberRelation;
use app\common\models\PayOrder;
use app\common\services\finance\BalanceChange;
use app\frontend\modules\finance\services\BalanceService;

class CODPay extends Pay
{

    public function doPay($params = [])
    {
        $operation = '货到付款支付 订单号：' . $params['order_no'];
        $this->log($params['extra']['type'], '货到付款', $params['amount'], $operation, $params['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        self::payRequestDataLog($params['order_no'], $params['extra']['type'], '货到付款', json_encode($params));

        $pay_order_model = PayOrder::uniacid()->where('out_order_no', $params['order_no'])->first();

        if ($pay_order_model) {
            $pay_order_model->status = 2;
            $pay_order_model->trade_no = $params['trade_no'];
            $pay_order_model->third_type = '货到付款';
            $pay_order_model->save();
        }

        return true;

    }

    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}