<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/21
 * Time: 下午4:23
 */

namespace app\common\events\member;

use app\common\events\Event;

abstract class SetMemberGoldEvent extends Event
{
    protected $data;
    protected $change_gold;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->getData();
    }
}