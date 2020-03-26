<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 上午10:09
 */

namespace app\frontend\modules\order\services;


use app\common\models\Order;
use app\common\models\order\OrderOperationLog;
use app\frontend\modules\order\services\behavior\OrderClose;

class AutoCancel
{
    //自动关闭
    public static function autoCancel()
    {
        $orders = Order::waitPay()->get();
        if ($orders) {
            self::query($orders);
        }
    }

    private static function query($orders)
    {
        foreach ($orders as $order)
        {
            $close_class = new OrderClose($order);
            if ($close_class->closeable()) {
                $close_class->close();
                //插入操作日志记录
                $log = [
                    'order_id'                  => $order->id,
                    'type'                      => '7',
                    'before_operation_status'   => '0',
                    'after_operation_status'    => '-1',
                    'operator'                  => 'php',
                    'operation_time'            => time()
                ];
                OrderOperationLog::insertOrderOperationLog($log);
            }
        }
    }
}