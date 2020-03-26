<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/4/16
 * Time: 下午1:08
 */

namespace app\Jobs;


use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use app\common\services\member\MemberRelation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MemberMaxRelatoinJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct($uniacid)
    {
        $this->uniacid  = $uniacid;
    }

    public function handle()
    {
        \Log::debug('-----queue max-----', $this->uniacid);

        $uniacid = $this->uniacid;
        $parentMemberModle = new ParentOfMember();
        $childMemberModel = new ChildrenOfMember();
        $parentMemberModle->DeletedData($uniacid);
        $childMemberModel->DeletedData($uniacid);

        $member_relation = new MemberRelation();

        $member_relation->createParentOfMember($uniacid);
    }
}