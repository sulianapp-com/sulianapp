<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/7/27
 * Time: 下午4:40
 */

namespace app\Jobs;


use app\common\events\member\RegisterByAgent;
use app\common\models\MemberShopInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Commission\models\Agents;

class ModifyRelationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 修改关系里的会员id
     *
     * @var
     */
    private $uid;

    /**
     * 新的会员父级关系链
     * @var
     */
    private $member_relation;

    /**
     * 分销插件是否开启
     * @var
     */
    private $plugin_commission;

    /**
     * 当前公众号
     * @var
     */
    private $uniacid;

    public function __construct($uid, $member_relation, $plugin_commission)
    {
        $this->uid = $uid;
        $this->member_relation = $member_relation;
        $this->plugin_commission = $plugin_commission;
    }

    public function handle()
    {
        $level = count($this->member_relation);
        \Log::debug('-----------relation-----------', $this->member_relation);

        if ($level > 1) {
            $first_relation = $this->uid . ',' . $this->member_relation[0] . ',' . $this->member_relation[1];
            $second_relation = $this->uid . ',' . $this->member_relation[0];
        } else {
            if ($this->member_relation[0] != 0) {
                $first_relation = $this->uid . ',' . $this->member_relation[0];
                $second_relation = $this->uid;
            } else {
                $first_relation = $this->uid;
                $second_relation = $this->uid;
            }
        }

        $this->ChangeFirstMember($this->uid, $first_relation, $this->plugin_commission);
        $this->ChangeSecondMember($this->uid, $second_relation, $this->plugin_commission);
    }

    /**
     * @param $uid
     * @param $relation
     * @param $open_plugin
     */
    private function ChangeFirstMember($uid, $relation, $open_plugin)
    {
        \Log::debug('----------ChangeFirstMember uid-------', $uid);
        $memberModel = $this->getMemberModel($uid, 1);
\Log::debug('----------ChangeFirstMember-------', count($memberModel));
        if (!$memberModel->isEmpty()) {
            foreach ($memberModel as $key => $model) {
                $model->relation = $relation;

                if ($model->save() && $open_plugin) {
                    $this->changeAgentRelation($model);
                }
            }
        }
    }

    private function ChangeSecondMember($uid, $relation, $open_plugin)
    {
        \Log::debug('----------ChangeSecondMember uid-------', $uid);
        $memberModel = $this->getMemberModel($uid, 2);
        \Log::debug('----------ChangeSecondMember-------', count($memberModel));
        if (!$memberModel->isEmpty()) {
            foreach ($memberModel as $key => $model) {
                if ($model->parent_id !== 0) {
                    $model->relation = $model->parent_id . ',' .$relation;

                    if ($model->save() && $open_plugin) {
                        $this->changeAgentRelation($model);
                    }
                }

            }
        }
    }

    private function getMemberModel($uid, $pos)
    {
        $memberModel = MemberShopInfo::getSubLevelMember($uid, $pos);

        return $memberModel;
    }

    private function changeAgentRelation($model)
    {
        $agents = Agents::getAgentByMemberId($model->member_id)->first();

        if (!is_null($agents)) {
            $agents->parent_id = $model->parent_id;
            $agents->parent    = $model->relation;

            $agents->save();
        }

        $agent_data = [
            'member_id' => $model->member_id,
            'parent_id' => $model->parent_id,
            'parent'   => $model->relation
        ];

       // event(new RegisterByAgent($agent_data));
    }
}