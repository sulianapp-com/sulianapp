<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/29
 * Time: 下午1:42
 */

namespace app\common\events\member;


use app\common\events\Event;

class MemberFirstChilderenEvent extends Event
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getInfo()
    {
        return $this->data;
    }
}