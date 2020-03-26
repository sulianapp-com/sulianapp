<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-11-01
 * Time: 01:20
 */

namespace app\common\events\finance;


use app\common\events\Event;

class LoveChangeEvent extends Event
{
    public $uid;

    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    public function getUid()
    {
        return $this->uid;
    }
}