<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-06-09
 * Time: 17:22
 */

namespace app\common\listeners\order;


use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\models\Order;
use app\common\facades\Setting;
use app\common\models\order\FirstOrder;

class FirstOrderListener
{
    public function handle(AfterOrderPaidEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'首单开始order_id:'.$order->id.PHP_EOL,1), FILE_APPEND);

        \YunShop::app()->uniacid = $order->uniacid;
        Setting::$uniqueAccountId = $order->uniacid;

        $shopOrderSet = Setting::get('shop.order');
        if (!$shopOrderSet['goods']) {
            // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'没设置'.PHP_EOL,1), FILE_APPEND);
            return;
        }

        if ($order->is_plugin != 0 || $order->plugin_id != 0) {
            // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'不是商城订单'.PHP_EOL,1), FILE_APPEND);
            return;
        }
        // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'setgoods:'.json_encode($shopOrderSet['goods'], 256).PHP_EOL,1), FILE_APPEND);

        foreach ($order->hasManyOrderGoods as $orderGoods) {
            // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'goods_id:'.$orderGoods->goods_id.PHP_EOL,1), FILE_APPEND);
            if ($shopOrderSet['goods'][$orderGoods->goods_id]) {

                $firstOrder = FirstOrder::select()
                    ->where('uid', $order->uid)
                    ->where('goods_id', $orderGoods->goods_id)
                    ->first();
                if ($firstOrder) {
                    // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'存在:'.$orderGoods->goods_id.PHP_EOL,1), FILE_APPEND);
                    continue;
                }
                // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'添加首单'.PHP_EOL,1), FILE_APPEND);
                FirstOrder::create([
                    'order_id' => $order->id,
                    'goods_id' => $orderGoods->goods_id,
                    'uid' => $order->uid,
                    'shop_order_set' => $shopOrderSet['goods']
                ]);
            } else {
                // file_put_contents(storage_path('logs/621test.txt'), print_r(date('Ymd His').'不存在'.PHP_EOL,1), FILE_APPEND);
            }
        }
    }

    public function cancel(AfterOrderCanceledEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);

        \YunShop::app()->uniacid = $order->uniacid;
        Setting::$uniqueAccountId = $order->uniacid;

        $ret = FirstOrder::select()
            ->where('order_id', $order->id)
            ->first();
        if ($ret) {
            $ret->delete();
        }
    }
}