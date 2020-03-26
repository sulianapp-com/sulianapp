<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/26
 * Time: 上午11:13
 */

namespace app\common\listeners\member;


use app\common\events\member\MemberChangeRelationEvent;
use app\common\services\member\MemberRelation;

class MemberChangeRelationEventListener
{
    public function handle(MemberChangeRelationEvent $event)
    {
        $member_id = $event->getUid();
        $parent_id = $event->getParentId();

        if (intval($member_id) > 0 && intval($parent_id) >= 0) {
            \Log::debug('修改会员关系');
            $member_relation = new MemberRelation();

            return $member_relation->change($member_id, $parent_id);
        }
    }
}