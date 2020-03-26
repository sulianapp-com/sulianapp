<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午8:47
 */

namespace app\common\listeners;

use app\common\events\PayLog;
use app\common\models\PayOrder;
use app\common\services\Pay;

class PayLogListener
{
    public function handle(PayLog $event)
    {
        $params = $event->getPayRequestParams();

        $pay_order_info = PayOrder::getPayOrderInfo($params['out_trade_no'])->first();

        if ($pay_order_info) {
            Pay::payRequestDataLog($params['out_trade_no'], $pay_order_info->type,
                $pay_order_info->third_type, json_encode($params));
        }
    }
}