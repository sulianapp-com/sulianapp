<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/3/29
 * Time: 9:44
 */

namespace app\common\events\member;


use app\common\events\Event;

class MemberGoodsHistoryEvent extends Event
{
    protected $goods_id;

    protected $mark;

    protected $mark_id;

    public function __construct($goods_id, $mark, $mark_id)
    {
        $this->goods_id = $goods_id;
        $this->mark = $mark;
        $this->mark_id = $mark_id;
    }

    public function getGoodsId()
    {
        return $this->goods_id;
    }

    public function getMark()
    {
        return $this->mark;
    }

    public function getMarkId()
    {
        return $this->mark_id;
    }
}