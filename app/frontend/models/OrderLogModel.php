<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/10
 * Time: 上午10:31
 */

namespace app\frontend\models;


use app\common\models\CorePayLog;
use app\common\models\Order;

class OrderLogModel extends CorePayLog
{
    public static function createLog(Order $order)
    {
        $log_data = [
            'uniacid'   => \YunShop::app()->uniacid,
            'member_id' => \YunShop::app()->getMemberId(),
            'tid'       => $order->hasOnePay->pay_sn,
            'fee'       => $order->price,
            'status'    => 0
        ];
        CorePayLog::create($log_data);
    }

    public static function getLog(Order $order)
    {
        return CorePayLog::select()->where('tid', '=', $order->hasOnePay->order_sn)->first();
    }
}