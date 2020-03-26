<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/2
 * Time: 下午4:28
 */

namespace app\backend\modules\order\services;


use app\backend\modules\order\models\Order;
use app\common\events\order\AfterOrderCanceledEvent;
use app\common\exceptions\AdminException;

class OrderService
{
    public static function close($order)
    {

        $order->status = Order::CLOSE;
        $order->cancel_time = time();
        $result = $order->save();
        event(new AfterOrderCanceledEvent($order));
        return $result;
    }
}