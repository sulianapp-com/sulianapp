<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/19
 * Time: 下午12:00
 */

namespace app\common\events\payment;


class RechargeComplatedEvent extends GetOrderPaymentTypeEvent
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getRechargeData()
    {
        return $this->data;
    }
}