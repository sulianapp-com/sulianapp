<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/23 下午2:16
 * Email: livsyitian@163.com
 */

namespace app\common\models;


use app\common\events\member\MemberCreateRelationEvent;
use app\common\events\member\MemberFirstChilderenEvent;
use app\common\events\member\MemberRelationEvent;
use app\common\events\MessageEvent;
use app\common\models\notice\MessageTemp;
use app\common\modules\member\MemberRelationRepository;
use app\common\services\MessageService;

class MemberRelation extends BaseModel
{
    static protected $needLog = true;

    public $table = 'yz_member_relation';

    public $timestamps = false;

    private static $orderId;

    /**
     * 可以批量赋值的属性
     *
     * @var array
     */
    public $fillable = ['uniacid', 'status', 'become', 'become_order', 'become_child', 'become_ordercount',
        'become_moneycount', 'become_goods_id', 'become_info', 'become_check', 'become_slefmoney','maximum_number','reward_points'];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    public $guarded = [];

    /**
     * 获取会员关系链数据
     * @return MemberRelation
     */
    public static function getSetInfo()
    {
        $memberRelation  = self::uniacid();
        // todo 优化重复查询问题,返回一个元素的集合是为了不影响历史代码的调用方式的,
//        $memberRelation = MemberRelationRepository::all()->where('uniacid',\YunShop::app()->uniacid);
//        $memberRelation = $memberRelation->map(function ($item) {
//           return new MemberRelation($item);
//        });
        return $memberRelation;
    }

    /**
     * 用户是否达到发展下线条件
     *
     * @return bool
     */
    public static function checkAgent($uid)
    {
        $info = self::getSetInfo()->first();

        if (empty($info)) {
            return [];
        }

        $member_info = MemberShopInfo::getMemberShopInfo($uid);

        if (!empty($member_info)) {
            $data = $member_info->toArray();
        }

        if ($data['is_agent'] == 0) {
            switch ($info['become']) {
                case 0:
                    $isAgent = true;
                    break;
                case 2:
                    $cost_num = Order::getCostTotalNum($uid);

                    if ($cost_num >= $info['become_ordercount']) {
                        $isAgent = true;
                    }
                    break;
                case 3:
                    $cost_price = Order::getCostTotalPrice($uid);

                    if ($cost_price >= $info['become_moneycount']) {
                        $isAgent = true;
                    }
                    break;
                case 4:
                    $isAgent = self::checkOrderGoods($info['become_goods_id'], $uid);
                    break;
                case 5:
                    $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
                    if ($sales_money >= $info['become_selfmoney']) {
                        $isAgent = true;
                    }
                    break;
                default:
                    $isAgent = false;
            }
        }

        if ($isAgent) {
            if ($info['become_check'] == 0) {
                $member_info->is_agent = 1;
                $member_info->status = 2;

                $member_info->save();
            }
        }
    }

    /**
     * 设置用户关系链
     *
     * @return void
     */
    public function setAgent()
    {
        $info = self::getSetInfo()->first()->toArray();

        $member_info = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId())->first();

        if (!empty($member_info)) {
            $data = $member_info->toArray();
        }

        $isAgent = false;
        if ($info['status'] == 1 && $data['is_agent'] == 0) {
            $mid = \app\common\models\Member::getMid();
            if ($mid != 0 && $data['member_id'] != $mid) {
                $member_info->parent_id = $mid;
                $member_info->save();
            }
        }
    }

    /**
     * 检查用户订单中是否包含指定商品
     *
     * @param $goods_id
     * @return bool
     */
    public static function checkOrderGoods($goods_id, $uid, $status)
    {
        $goods_ids = explode(',',$goods_id);
        $list = OrderGoods::uniacid()
            ->where('uid',$uid)
            ->whereIn('goods_id', $goods_ids)
            ->whereHas('hasOneOrder',function ($query) use($status) {
                $query->where('status', '>=', $status);
            })
            ->get();
        if ($list->isEmpty()) {
            return false;
        }
        return true;
    }

    /**
     * 获取成为下线条件
     *
     * @return int
     */
    public function getChildAgentInfo()
    {
        $info = self::getSetInfo()->first();

        if (!empty($info)) {

            return $info->become_child;
        }
    }

    /**
     * 成为下线
     *
     * @param $mid
     * @param MemberShopInfo $model
     */
    private function changeChildAgent($mid, MemberShopInfo $model)
    {
        \Log::debug(sprintf('成为下线mid-%d', $mid));
        $member_info = MemberShopInfo::getMemberShopInfo($mid);

        if ($member_info && $member_info->is_agent) {
            $model->parent_id = $mid;
            $model->child_time = time();

            if ($model->save()) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * 检查是否能成为下线
     *
     * 首次点击分享连接 / 无条件发展下线权利
     *
     * 触发 入口
     *
     * @param $mid
     * @param MemberShopInfo $user
     */
    public function becomeChildAgent($mid, \app\common\models\MemberShopInfo $model)
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = MemberShopInfo::getMemberShopInfo($model->member_id);

        if (empty($member)) {
            return;
        }

        if ($member->is_agent == 1) {
            return;
        }

        $parent = null;

        $become_child =  intval($set->become_child);
        $become_check = intval($set->become_check);

        if (!empty($mid)) {
            $parent =  MemberShopInfo::getMemberShopInfo($mid);
        } else {
            if ($member->inviter == 0 && $member->parent_id == 0) {
                if (empty($become_child)) {
                    $model->child_time = time();
                    $model->inviter = 1;
                    \Log::debug(sprintf('会员id-%d确定上线id-%d', $model->member_id, $mid));

                    $model->save();
                }
            }
        }

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;
        $curr_parent_id = $model->parent_id;

        if ($parent_is_agent && empty($member->inviter)) {
            if ($member->member_id != $parent->member_id) {
                $this->changeChildAgent($mid, $model);

                if (empty($become_child)) {
                    $model->inviter = 1;
                    \Log::debug(sprintf('会员id-%d确定上线id-%d', $model->member_id, $mid));

                    self::rewardPoint($model->parent_id, $model->member_id);

                    //notice
                    self::sendAgentNotify($member->member_id, $mid);
                } else {
                    \Log::debug(sprintf('会员id-%d未确定上线id-%d', $model->member_id, $mid));
                    $model->inviter = 0;
                }

                $model->save();

                if ($curr_parent_id != $model->parent_id) {
                    event(new MemberCreateRelationEvent($model, $mid));
                    event(new MemberFirstChilderenEvent(['member_id' => $mid]));
                }

            }
        }

        if (empty($set->become) ) {
            $model->is_agent = 1;

            if ($become_check == 0) {
                $model->status = 2;
                $model->agent_time = time();

                if ($model->inviter == 0) {
                    $model->inviter = 1;
                }
            } else {
                $model->status = 1;
            }

            if ($model->save()) {
                self::setRelationInfo($model, $curr_parent_id);
            }
        }
    }

    /**
     * 成为下线条件 首次下单
     *
     * 触发 确认订单
     *
     * @return void
     */
    public static function checkOrderConfirm($uid)
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = MemberShopInfo::getMemberShopInfo($uid);

        if (empty($member)) {
            return;
        }

        $curr_parent_id = $member->parent_id;
        $become_child = intval($set->become_child);

        if ($member->parent_id == 0) {
            \Log::debug(sprintf('会员上线ID进入时1-: %d', $member->parent_id));
            if ($become_child == 1 && empty($member->inviter)) {
                $member->child_time = time();
                $member->inviter = 1;

                $member->save();
            }
        } else {
            $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);
            \Log::debug(sprintf('会员上线ID进入时2-: %d', $member->parent_id));
            $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

            if ($parent_is_agent) {
                if ($become_child == 1) {
                    if (empty($member->inviter) && $member->member_id != $parent->member_id) {
                        \Log::debug(sprintf('会员赋值 parent_id: %d', $parent->member_id));
                        $member->parent_id = $parent->member_id;
                        $member->child_time = time();
                        $member->inviter = 1;

                        self::rewardPoint($member->parent_id, $member->member_id);

                        $member->save();

                        if ($curr_parent_id != $member->parent_id) {
                            event(new MemberCreateRelationEvent($member, $member->parent_id));
                        }

                        //message notice
                        self::sendAgentNotify($member->member_id, $parent->member_id);
                    }
                }
            }
        }

        if ($curr_parent_id != $member->parent_id) {
            event(new MemberFirstChilderenEvent(['member_id' => $uid]));
        }
    }

    /**
     * @name 发展下线资格 付款后 成为下线条件 首次付款触发 支付回调
     * @author
     * @param $uid
     * @param int $orderId
     */
    public static function checkOrderPay($uid, $orderId = 0)
    {
        // Yy edit:2019-03-06
        self::$orderId = $orderId;

        $set = self::getSetInfo()->first();
        $become_check = intval($set->become_check);

        \Log::debug('付款后');
        if (empty($set)) {
            return;
        }

        $member = MemberShopInfo::getMemberShopInfo($uid);
        if (empty($member)) {
            return;
        }
        \Log::debug(sprintf('会员上线-%d', $member->parent_id));
        $become_child = intval($set->become_child);
        $curr_parent_id = $member->parent_id;

        $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

        //成为下线
        if ($member->parent_id == 0) {
            if ($become_child == 2 && empty($member->inviter)) {
                $member->child_time = time();
                $member->inviter = 1;
                $member->save();
            }
        } else {
            if ($parent_is_agent) {
                if ($become_child == 2) {
                    if (empty($member->inviter) && $member->member_id != $parent->member_id) {
                        $member->parent_id = $parent->member_id;
                        $member->child_time = time();
                        $member->inviter = 1;

                        self::rewardPoint($member->parent_id, $member->member_id);

                        $member->save();

                        if ($curr_parent_id != $member->parent_id) {
                            event(new MemberCreateRelationEvent($member, $member->parent_id));
                        }

                        //message notice
                        self::sendAgentNotify($member->member_id, $parent->member_id);
                    }
                }
            }
        }

        //发展下线资格
        $isagent = $member->is_agent == 1 && $member->status == 2;

        \Log::debug('会员成为推广员',$isagent);
        \Log::debug('会员成为推广员设置',$set);
        if (!$isagent && empty($set->become_order)) {
            $become_term = unserialize($set->become_term);
            //或
            if ($set->become == 2) {
                self::eitherCondition($become_term, $set, $uid, $member, $become_check, 1);
            }
            //与
            if ($set->become == 3) {
                self::andCondition($become_term, $set, $uid, $member, $become_check, 1);
            }
        }
    }

    /**
     * @name 发现下线资格 完成后 触发 订单完成
     * @author
     * @param $uid
     * @param int $orderId
     */
    public static function checkOrderFinish($uid, $orderId = 0)
    {
        // Yy edit:2019-03-06
        self::$orderId = $orderId;

        $set = self::getSetInfo()->first();
        $become_check = intval($set->become_check);

        \Log::debug('订单完成');

        if (empty($set)) {
            return;
        }
        \Log::debug('关系链设置');
        $member = MemberShopInfo::getMemberShopInfo($uid);

        if (empty($member)) {
            return;
        }

        $isagent = $member->is_agent == 1 && $member->status == 2;

        \Log::debug('会员成为推广员',$isagent);
        \Log::debug('会员成为推广员设置',$set);
        if (!$isagent && $set->become_order == 1) {
            $become_term = unserialize($set->become_term);
            //如果设置为空时添加默认值，防止程序出错
            if (empty($set->become) && !empty($become_term)) {
                $set->become = 2;
            }

            //或
            if ($set->become == 2) {
                self::eitherCondition($become_term, $set, $uid, $member, $become_check, 3);
            }
            //与
            if ($set->become == 3) {
                self::andCondition($become_term, $set, $uid, $member, $become_check,3);
            }
        }
    }

    public static function eitherCondition($become_term, $set, $uid, $member, $become_check, $status)
    {
        $curr_parent_id = $member->parent_id;

        //判断商品
        if ($become_term[4] == 4 && !empty($set->become_goods_id)) {
            $result = self::checkOrderGoods($set->become_goods_id, $uid, $status);

            if ($result) {
                $member->is_agent = 1;

                if ($become_check == 0) {
                    $member->status = 2;
                    $member->agent_time = time();
                    $member->apply_time = time();

                    if ($member->inviter == 0) {
                        $member->inviter = 1;
                    }
                } else {
                    $member->status = 1;
                    $member->agent_time = time();
                    $member->apply_time = time();
                }

                if ($member->save()) {
                    self::setRelationInfo($member, $curr_parent_id);
                    return;
                }
            }
        }
        //消费达多少次
        if ($become_term[2] == 2) {
            $ordercount = Order::getCostTotalNum($member->member_id);
            \Log::debug('用户：'. $ordercount);
            \Log::debug('系统：'. intval($set->become_ordercount));
            $can = $ordercount >= intval($set->become_ordercount);

            if ($can) {
                $member->is_agent = 1;

                if ($become_check == 0) {
                    $member->status = 2;
                    $member->agent_time = time();
                    $member->apply_time = time();

                    if ($member->inviter == 0) {
                        $member->inviter = 1;
                    }
                } else {
                    $member->status = 1;
                    $member->agent_time = time();
                    $member->apply_time = time();
                }

                if ($member->save()) {
                    self::setRelationInfo($member, $curr_parent_id);
                    return;
                }
            }
        }
        //消费达多少钱
        if ($become_term[3] == 3) {

            $moneycount = Order::getCostTotalPrice($member->member_id);
            $can = $moneycount >= floatval($set->become_moneycount);
            if ($can) {
                $member->is_agent = 1;

                if ($become_check == 0) {
                    $member->status = 2;
                    $member->agent_time = time();
                    $member->apply_time = time();

                    if ($member->inviter == 0) {
                        $member->inviter = 1;
                    }
                } else {
                    $member->status = 1;
                    $member->agent_time = time();
                    $member->apply_time = time();
                }

                if ($member->save()) {
                    self::setRelationInfo($member, $curr_parent_id);
                    return;
                }
            }
        }
        //销售佣金
        if ($become_term[5] == 5) {
            $can = false;

            $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
            if ($sales_money >= $set->become_selfmoney) {
                $can = true;
            }

            if ($can) {
                $member->is_agent = 1;

                if ($become_check == 0) {
                    $member->status = 2;
                    $member->agent_time = time();
                    $member->apply_time = time();

                    if ($member->inviter == 0) {
                        $member->inviter = 1;
                    }
                } else {
                    $member->status = 1;
                    $member->agent_time = time();
                    $member->apply_time = time();
                }

                if ($member->save()) {
                    self::setRelationInfo($member, $curr_parent_id);
                    return;
                }
            }
        }
    }

    public static function andCondition($become_term, $set, $uid, $member, $become_check, $status)
    {
        $curr_parent_id = $member->parent_id;

        //判断商品
        if ($become_term[4] == 4 && !empty($set->become_goods_id)) {
            $result = self::checkOrderGoods($set->become_goods_id, $uid, $status);
            if (!$result) {
                return;
            }
        }

        //判断消费达多少次
        if ($become_term[2] == 2) {
            $ordercount = Order::getCostTotalNum($member->member_id);
            \Log::debug('用户：'. $ordercount);
            \Log::debug('系统：'. intval($set->become_ordercount));
            $can = $ordercount >= intval($set->become_ordercount);

            if (!$can) {
                return;
            }
        }

        //消费达多少元
        if ($become_term[3] == 3) {
            $moneycount = Order::getCostTotalPrice($member->member_id);
            $can = $moneycount >= floatval($set->become_moneycount);

            if (!$can) {
                return;
            }
        }

        //销售佣金
        if ($become_term[5] == 5) {
            $can = false;

            $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
            if ($sales_money >= $set->become_selfmoney) {
                $can = true;
            }
            if (!$can) {
                return;
            }
        }

        //以上条件全部满足则升级
        $member->is_agent = 1;

        if ($become_check == 0) {
            $member->status = 2;
            $member->agent_time = time();
            $member->apply_time = time();

            if ($member->inviter == 0) {
                $member->inviter = 1;
            }
        } else {
            $member->status = 1;
            $member->agent_time = time();
            $member->apply_time = time();
        }

        if ($member->save()) {
            self::setRelationInfo($member, $curr_parent_id);
        }

    }

    /**
     * 获得推广权限通知
     *
     * @param $uid
     */
    public static function sendGeneralizeNotify($uid)
    {
        \Log::debug('获得推广权限通知');

        $member = Member::getMemberByUid($uid)->with('hasOneFans')->first();

        // Yy edit:2019-03-06
        if (!isset(self::$orderId)) {
            self::$orderId = 0;
        }
        // Yy edit:2019-03-06
        event(new MemberRelationEvent($member, self::$orderId));

        $member->follow = $member->hasOneFans->follow;
        $member->openid = $member->hasOneFans->openid;

        $uniacid = \YunShop::app()->uniacid ?: $member->uniacid;

        self::generalizeMessage($member, $uniacid);
    }

    public static function generalizeMessage($member, $uniacid)
    {
        $noticeMember = Member::getMemberByUid($member->uid)->with('hasOneFans')->first();

        if (!$noticeMember->hasOneFans->openid) {
            return;
        }

        $temp_id = \Setting::get('relation_base')['member_agent'];

        if (!$temp_id) {
            return;
        }

        $params = [
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())]
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);

        if (!$msg) {
            return;
        }

        event(new MessageEvent($member->uid, $temp_id, $params, $url=''));
    }

    /**
     * 新增下线通知
     *
     * @param $uid
     */
    public static function sendAgentNotify($uid, $puid)
    {
        \Log::debug('新增下线通知');
        $parent = Member::getMemberByUid($puid)->with('hasOneFans')->first();
        $parent->follow = $parent->hasOneFans->follow;
        $parent->openid = $parent->hasOneFans->openid;

        $member = Member::getMemberByUid($uid)->first();

        $uniacid = \YunShop::app()->uniacid ?: $parent->uniacid;

        self::agentMessage($parent, $member, $uniacid);
    }

    public static function agentMessage($parent, $member, $uniacid)
    {
        $noticeMember = Member::getMemberByUid($parent->uid)->with('hasOneFans')->first();

        if (!$noticeMember->hasOneFans->openid) {
            return;
        }

        $temp_id = \Setting::get('relation_base')['member_new_lower'];

        if (!$temp_id) {
            return;
        }

        $params = [
            ['name' => '昵称', 'value' => $parent->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '下级昵称', 'value' => $member->nickname]
        ];

        event(new MessageEvent($parent->uid, $temp_id, $params, $url=''));
    }

    private static function setRelationInfo($member, $curr_parent_id)
    {
        if ($member->is_agent == 1 && $member->status == 2) {
            if ($curr_parent_id != $member->parent_id) {
                Member::setMemberRelation($member->member_id,$member->parent_id);
            }

            //message notice
            self::sendGeneralizeNotify($member->member_id);
        }
    }

    public static function rewardPoint($parent_id, $member_id) {
        $memberRelation = new \app\common\services\member\MemberRelation();
        $memberRelation->rewardPoint($parent_id, $member_id);
    }
}
