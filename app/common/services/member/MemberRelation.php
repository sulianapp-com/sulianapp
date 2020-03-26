<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 上午11:52
 */

namespace app\common\services\member;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\exceptions\ShopException;
use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use Illuminate\Support\Facades\DB;
use app\backend\modules\member\models\MemberRelation as Relation;
use app\backend\modules\member\models\MemberParent;
use app\common\models\notice\MessageTemp;
use app\common\events\MessageEvent;
use app\common\services\finance\PointService;



class MemberRelation
{
    public $parent;
    public $child;
    public $map_relaton = [];
    public $map_parent = [];
    public $map_parent_total = 0;

    public function __construct()
    {
        $this->parent = new ParentOfMember();
        $this->child  = new ChildrenOfMember();
    }

    /**
     * 批量统计会员父级
     *
     */
    public function createParentOfMember($uniacid = null)
    {
        ini_set("memory_limit","-1");

        \Log::debug('------queue parent start-----');

        if (is_null($uniacid)) {
            $uniacid = \YunShop::app()->uniacid;
        }

        $pageSize = 2000;
        $total = Member::getAllMembersInfosByQueue($uniacid)->distinct()->count();

        $total_page  = ceil($total/$pageSize);

        \Log::debug('------total-----', $total);
        \Log::debug('------total_page-----', $total_page);

        for ($curr_page = 1; $curr_page <= $total_page; $curr_page++) {
            \Log::debug('------curr_page-----', $curr_page);
            $offset      = ($curr_page - 1) * $pageSize;

            $job = (new \app\Jobs\memberParentOfMemberJob($uniacid, $pageSize, $offset));
            dispatch($job);
        }
    }

    /**
     * 批量统计会员子级
     *
     */
    public function createChildOfMember()
    {
        \Log::debug('------queue child start-----');
        $job = (new \app\Jobs\memberChildOfMemberJob(\YunShop::app()->uniacid));
        dispatch($job);
    }

    /**
     * 获取会员指定层级的子级
     *
     * @param $uid
     * @param $depth
     * @return mixed
     */
    public function getMemberByDepth($uid, $depth)
    {
          $this->child->getMemberByDepth($uid, $depth);
    }

    /**
     * 添加会员关系
     *
     */
    public function addMemberOfRelation($uid, $parent_id)
    {
        try {
            DB::transaction(function() use ($uid, $parent_id) {
                $this->parent->addNewParentData($uid, $parent_id);

                $this->child-> addNewChildData($this->parent, $uid, $parent_id);
            });

            return true;
        } catch (\Exception $e) {
            \Log::debug('-------member relation add error-----', [$e->getMessage()]);
            return false;
        }

    }

    /**
     * 删除会员关系
     *
     * @param $uid
     * @throws \Exception
     * @throws \Throwable
     */
    public function delMemberOfRelation($uid, $n_parent_id)
    {
        DB::transaction(function() use ($uid, $n_parent_id) {
            \Log::debug('--------setp5-------');
            $this->child->delMemberOfRelation($this->parent, $uid, $n_parent_id);

            $this->parent->delMemberOfRelation($this->child, $uid, $n_parent_id);
        });
    }

    /**
     * 修改后重新添加
     *
     * @param $uid
     * @param $n_parent_id
     */
    public function reAddMemberOfRelation()
    {
        foreach ($this->map_relaton as $reData) {
            $this->addMemberOfRelation($reData[1], $reData[0]);
        }
    }

    /**
     * 修改会员关系
     *
     * @param $uid
     * @param $o_parent_id
     * @param $n_parent_id
     * @throws \Exception
     * @throws \Throwable
     */
    public function changeMemberOfRelation($uid, $n_parent_id)
    {
        try {
            DB::transaction(function() use ($uid, $n_parent_id) {
                $this->delMemberOfRelation($uid, $n_parent_id);

                if ($n_parent_id) {
                    \Log::debug('------step4-------', $n_parent_id);
                    $this->reAddMemberOfRelation();
                }
            });

            return true;
        } catch (\Exception $e) {
            \Log::debug('------修改会员关系error----', [$e->getMessage()]);

            return false;
        }
    }

    public function hasRelationOfParent($uid, $depth)
    {
        return $this->parent->hasRelationOfParent($uid, $depth);
    }

    public function hasRelationOfChild($uid)
    {
        return $this->child->hasRelationOfChild($uid);
    }

    public function build($member_id, $parent_id, $model = null)
    {
        $parent_relation = $this->hasRelationOfParent($member_id, 1);

        if ($parent_relation->isEmpty() && intval($parent_id) > 0) {
            \Log::debug('------step1-------');
            if ($this->addMemberOfRelation($member_id, $parent_id)) {
                return ['status' => 1];
            }
        } else {
            if ($parent_relation[0]->parent_id != $parent_id) {
                return $this->change($member_id, $parent_id);
            }
        }

        return ['status' => 0];
    }

    public function change($member_id, $parent_id)
    {
        if ($member_id != $parent_id) {
            $parent_relation = $this->hasRelationOfParent($member_id, 1);
            $child_relation = $this->hasRelationOfChild($member_id);

            \Log::debug('------step2-------');
            $this->map_relaton[] = [$parent_id, $member_id];

            foreach ($child_relation as $rows) {
                $ids[] = $rows['child_id'];
            }

            $ids = array_unique($ids);
            $memberInfo = MemberShopInfo::getParentOfMembeWithTrashed($ids);

            if (count($ids) != count($memberInfo)) {
                throw new ShopException('关系链修改-数据异常');
            }

            foreach ($ids as $rows) {
                foreach ($memberInfo as $val) {
                    if ($rows == $val['member_id']) {
                        $this->map_relaton[] = [$val['parent_id'], $val['member_id']];

                        break;
                    }
                }
            }

            file_put_contents(storage_path("logs/" . date('Y-m-d') . "_changerelation.log"), print_r($member_id . '-'. $parent_relation[0]->parent_id . '-'. $parent_id . PHP_EOL, 1), FILE_APPEND);
            if ($this->changeMemberOfRelation($member_id, $parent_id)) {
//                //绑定下线成功赠送积分
//                $this->rewardPoint($parent_id,$member_id);
                return ['status' => 1];
            }
        }

        return ['status' => 0];
    }
    //成为下线奖励积分
    public function  rewardPoint($parent_id,$member_id){

        $relation = Relation::getSetInfo()->first();
        //奖励积分
        $reward_points  = $relation->reward_points;
        //最大奖励人数
        $maxinum_number = $relation->maximum_number;

        $total = MemberParent::where([
            ['parent_id','=',$parent_id],
            ['level','=',1],
        ])->count();

        \Log::debug('会员成为下线奖励积分',$reward_points.'--'.$maxinum_number.'---'.$total);

        if( $total <= $maxinum_number || empty($maxinum_number)){
            //团队下线小于设置的最大奖励人数就奖励积分
            $memberModel = Member::where('uid',$parent_id)->first();
            $pointData = array(
                'uniacid' => \YunShop::app()->get('uniacid'),
                'point_income_type' => PointService::POINT_INCOME_GET,
                'member_id' => $memberModel->uid,
                'point_mode' => PointService::POINT_MODE_PRESENTATION,
                'point' => $reward_points,
                'remark' => '------会员ID为----'.$member_id.'成为会员ID为'.$parent_id.'的下线奖励积分'.$reward_points.'个',
            );
            try {
                $pointService = new PointService($pointData);
                $pointService->changePoint();
                \Log::debug('------会员ID为----'.$member_id.'成为会员ID为'.$parent_id.'的下线奖励积分'.$reward_points);
            } catch (\Exception $e) {
                \Log::error('成为下线积分奖励出错:' . $e->getMessage());
            }

        }

    }

    /**
     * 修复会员关系
     *
     * @param $uid
     * @param $parent_id
     * @throws \Exception
     * @throws \Throwable
     */
    public function fixMemberOfRelation($uid, $parent_id)
    {
        DB::transaction(function() use ($uid, $parent_id) {
            $this->parent->fixParentData($uid, $parent_id);

            $this->child-> fixChildData($this->parent, $uid, $parent_id);
        });
    }

    public function fixData($member_id, $parent_id)
    {
        $this->fixMemberOfRelation($member_id, $parent_id);
    }
}