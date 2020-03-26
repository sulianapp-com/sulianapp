<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/6
 * Time: 11:18
 */

namespace app\common\listeners\order;

use app\common\models\Order;
use app\common\events\order\AfterOrderCreatedEvent;

class OrderNoRefundListener
{
    protected $orderModel;

    public function subscribe($event)
    {
        // 订单生成后，循环判断订单中的商品是否有不可退换货的商品，如果有，则将订单的no_refund字段设置为1
        // 设置后，在订单支付之后，在前端查看订单时，就不会出现申请退货按钮
        $event->listen(AfterOrderCreatedEvent::class, OrderNoRefundListener::class. '@noRefund');
    }

    public function noRefund(AfterOrderCreatedEvent $event)
    {
        $orderModel = Order::find($event->getOrderModel()->id);
        $orderModel->no_refund = $this->isNotRefund($orderModel);
        $orderModel->save();
    }

    public function isNotRefund($order)
    {
        if ($order->hasManyOrderGoods) {
            foreach ($order->hasManyOrderGoods as $goods) {
                if ($goods->hasOneGoods->no_refund) {
                    return 1;
                }
            }
        }
        return 0;
    }

}