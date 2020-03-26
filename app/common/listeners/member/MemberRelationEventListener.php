<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/30
 * Time: 上午9:49
 */

namespace app\common\listeners\member;


use app\common\events\member\MemberRelationEvent;
use app\common\services\member\MemberRelation;

class MemberRelationEventListener
{
    public function handle(MemberRelationEvent $event)
    {
        /*$yzMemberModel = $event->getMemberModel()->yzMember;
        $member_id = $yzMemberModel->member_id;
        $parent_id = $yzMemberModel->parent_id;
        \Log::info('会员关系' . $yzMemberModel->member_id);

        (new MemberRelation())->addMemberOfRelation($member_id, $parent_id);*/
    }
}