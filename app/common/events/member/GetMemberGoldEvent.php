<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/21
 * Time: 下午3:22
 */

namespace app\common\events\member;

use app\common\events\Event;

abstract class GetMemberGoldEvent extends Event
{
    protected $member_id;
    protected $change_gold;

    public function __construct($member_id, $change_gold)
    {
        $this->member_id = $member_id;
        $this->change_gold = $change_gold;
    }

    public function getMemberIdAndGold()
    {
        return [
            'member_id' => $this->member_id,
            'gold'      => $this->change_gold
        ];
    }
}