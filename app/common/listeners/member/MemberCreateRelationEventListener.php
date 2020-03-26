<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/30
 * Time: 下午4:20
 */

namespace app\common\listeners\member;


use app\common\events\member\MemberCreateRelationEvent;
use app\common\services\member\MemberRelation;

class MemberCreateRelationEventListener
{
    public function handle(MemberCreateRelationEvent $event)
    {
        $member_id = $event->getUid();
        $parent_id = $event->getParentId();
        $member_model     = $event->getMemberModel();

        if (intval($member_id) > 0 && intval($parent_id) >= 0) {
            \Log::debug('创建会员关系');
            $member_relation = new MemberRelation();

            return $member_relation->build($member_id, $parent_id, $member_model);
        }
    }
}