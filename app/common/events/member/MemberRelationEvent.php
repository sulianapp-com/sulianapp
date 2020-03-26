<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午3:48
 */

namespace app\common\events\member;


use app\common\events\Event;
use app\common\models\Member;

class MemberRelationEvent extends Event
{
    protected $user;

    // Yy edit:2019-03-06
    protected $orderId;

    public function __construct(Member $model, $orderId = 0)
    {
        $this->user = $model;
        // Yy edit:2019-03-06
        $this->orderId = $orderId;
    }

    public function getMemberModel()
    {
        return $this->user;
    }

    // Yy edit:2019-03-06
    public function getOrderId()
    {
        return $this->orderId;
    }
}