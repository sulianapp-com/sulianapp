<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 下午1:38
 */

namespace app\common\events\order;
use app\common\events\Event;


class PreOrderDisplayEvent extends Event
{
    private $_order_model;

    public function __construct($order_model)
    {
        $this->_order_model = $order_model;
    }

    public function getOrderModel()
    {
        return $this->_order_model;
    }
}