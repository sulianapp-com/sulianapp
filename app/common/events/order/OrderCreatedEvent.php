<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 上午11:44
 */

namespace app\common\events\order;

use app\common\events\Event;
use app\common\models\Order;


class OrderCreatedEvent extends Event
{
    /**
     * @var Order
     */
    private $_order_model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order_model)
    {
        $this->_order_model = $order_model;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order_model;;
}

    /**
     * (监听者)获取订单model
     * @return mixed
     */
    public function getOrderModel()
    {
        return $this->_order_model;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}