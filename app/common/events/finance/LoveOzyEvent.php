<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/16
 * Time: 15:35
 */

namespace app\common\events\finance;


use app\common\events\Event;

class LoveOzyEvent extends Event
{
    public $award ;
    public $orderGoods;

    public function __construct($goods,$award)
    {
        $this->orderGoods = $goods;
        $this->award = $award;
    }

    public function setStatus($status)
    {
        $this->award = $status;
    }

    public function getStatus()
    {
        return $this->award;
    }

    public function getOrder()
    {
        return $this->orderGoods;
    }
}