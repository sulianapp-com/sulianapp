<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 上午9:36
 */

namespace app\common\events\order;


use app\common\events\Event;
use app\common\models\Order;
use app\frontend\modules\order\models\PreOrder;

abstract class CreatedOrderEvent extends Event
{
    protected $orderModel;
    /**
     * @var Order
     */
    protected $order;

    /**
     * CreatedOrderEvent constructor.
     * @param Order $order
     */
    public function __construct($order)
    {

        $this->order = $order;
    }
    /**
     * (监听者)获取订单model
     * @return Order
     */
    public function getOrderModel(){
        return $this->order;
    }

    /**
     * @return Order
     */
    public function getOrder(){
        return $this->order;
    }

}