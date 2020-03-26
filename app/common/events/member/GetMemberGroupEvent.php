<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/20
 * Time: 下午7:07
 */

namespace app\common\events\member;

use app\common\events\Event;

abstract class GetMemberGroupEvent extends Event
{
    protected $member_id;

    public function __construct($member_id)
    {
        $this->member_id = $member_id;
    }

    public function getMemberId()
    {
        return $this->member_id;
    }
}