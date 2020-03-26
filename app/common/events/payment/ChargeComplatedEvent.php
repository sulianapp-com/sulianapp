<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/20
 * Time: 下午3:43
 */

namespace app\common\events\payment;


use app\common\events\Event;

class ChargeComplatedEvent extends Event
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getChargeData()
    {
        return $this->data;
    }
}