<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 上午10:09
 */

namespace app\frontend\modules\order\services;


use app\common\models\Order;
use app\frontend\modules\order\services\behavior\OrderReceive;

class AutoComplete
{
    //自动完成
    public static function autoComplete()
    {
        $orders = Order::waitReceive()->get();
        if ($orders) {
            self::query($orders);
        }
    }

    private static function query($orders)
    {
        foreach ($orders as $order)
        {
            $receive_class = new OrderReceive($order);
            if ($receive_class->receiveable()) {
                $receive_class->receive();
            }
        }
    }
}