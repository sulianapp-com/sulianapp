<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/10
 * Time: 下午5:49
 */

namespace app\common\services\finance;


use app\backend\modules\member\models\Member;
use app\common\events\MessageEvent;
use app\common\exceptions\ShopException;
use app\common\models\finance\PointLog;
use app\common\models\notice\MessageTemp;
use Setting;

class PointService
{
    const POINT_INCOME_GET = 1; //获得

    const POINT_INCOME_LOSE = -1; //失去

    const POINT_MODE_GOODS = 1; //商品赠送
    const POINT_MODE_GOODS_ATTACHED = '商品赠送';

    const POINT_MODE_ORDER = 2; //订单赠送
    const POINT_MODE_ORDER_ATTACHED = '订单赠送';

    const POINT_MODE_POSTER = 3; //超级海报
    const POINT_MODE_POSTER_ATTACHED = '超级海报';

    const POINT_MODE_ARTICLE = 4; //文章营销
    const POINT_MODE_ARTICLE_ATTACHED = '文章营销';

    const POINT_MODE_ADMIN = 5; //后台充值
    const POINT_MODE_ADMIN_ATTACHED = '后台充值';

    const POINT_MODE_BY = 6; //购物抵扣
    const POINT_MODE_BY_ATTACHED = '购物抵扣';
    
    const POINT_MODE_TEAM = 7; //团队奖励
    const POINT_MODE_TEAM_ATTACHED = '团队奖励';

    const POINT_MODE_LIVE = 8; //生活缴费奖励
    const POINT_MODE_LIVE_ATTACHED = '生活缴费奖励';

    const POINT_MODE_AIR = 10; //飞机票
    const POINT_MODE_AIR_ATTACHED = '飞机票奖励';

    const POINT_MODE_CASHIER = 9; //收银台奖励
    const POINT_MODE_CASHIER_ATTACHED = '收银台奖励';

    const POINT_MODE_STORE = 93; //收银台奖励
    const POINT_MODE_STORE_ATTACHED = '门店奖励';

    const POINT_MODE_HOTEL_CASHIER = 28; //酒店收银台奖励
    const POINT_MODE_HOTEL_CASHIER_ATTACHED = '酒店收银台奖励';

    const POINT_MODE_HOTEL = 94; //酒店奖励
    const POINT_MODE_HOTEL_ATTACHED = '酒店奖励';

    const POINT_MODE_RECHARGE = 11; //话费充值奖励
    const POINT_MODE_RECHARGE_ATTACHED = '话费充值奖励';

    const POINT_MODE_FLOW = 12; //流量充值奖励
    const POINT_MODE_FlOW_ATTACHED = '流量充值奖励';

    const POINT_MODE_TRANSFER = 13; //转让
    const POINT_MODE_TRANSFER_ATTACHED = '转让-转出';

    const POINT_MODE_RECIPIENT = 14; //转让
    const POINT_MODE_RECIPIENT_ATTACHED = '转让-转入';

    const POINT_MODE_ROLLBACK = 15; //回滚
    const POINT_MODE_ROLLBACK_ATTACHED = '积分返还';

    const POINT_MODE_COUPON_DEDUCTION_AWARD = 16;
    const POINT_MODE_COUPON_DEDUCTION_AWARD_ATTACHED = '优惠券抵扣奖励';

    const POINT_MODE_TRANSFER_LOVE = 18;
    const POINT_MODE_TRANSFER_LOVE_ATTACHED = '自动转出';

    const POINT_MODE_RECHARGE_CODE = 92;
    const POINT_MODE_RECHARGE_CODE_ATTACHED = '充值码充值积分';


    const POINT_MODE_TASK_REWARD = 17;
    const POINT_MODE_TASK_REWARD_ATTACHED = '任务奖励';

    const POINT_MODE_SIGN_REWARD = 19;
    const POINT_MODE_SIGN_REWARD_ATTACHED = '签到奖励';

    const POINT_MODE_COURIER_REWARD = 20;
    const POINT_MODE_COURIER_REWARD_ATTACHED = '快递单奖励';

    const POINT_MODE_FROZE_AWARD = 21;
    const POINT_MODE_FROZE_AWARD_ATTACHED = '冻结币奖励';

    const POINT_MODE_COMMUNITY_REWARD = 22;
    const POINT_MODE_COMMUNITY_REWARD_ATTACHED = '圈子签到奖励';

    const POINT_MODE_CREATE_ACTIVITY = 23;
    const POINT_MODE_CREATE_ACTIVITY_ATTACHED = '创建活动';


    const POINT_MODE_ACTIVITY_OVERDUE = 24;
    const POINT_MODE_ACTIVITY_OVERDUE_ATTACHED = '活动失效';


    const POINT_MODE_RECEIVE_ACTIVITY = 25;
    const POINT_MODE_RECEIVE_ACTIVITY_ATTACHED = '领取活动';


    const POINT_MODE_RECEIVE_OVERDUE = 26;
    const POINT_MODE_RECEIVE_OVERDUE_ATTACHED = '领取失效';

    const POINT_MODE_COMMISSION_TRANSFER = 27;
    const POINT_MODE_COMMISSION_TRANSFER_ATTACHED = '分销佣金转入';

    const POINT_MODE_EXCEL_RECHARGE = 29;
    const POINT_MODE_EXCEL_RECHARGE_ATTACHED = 'EXCEL充值';

    const POINT_MODE_CARD_VISIT_REWARD = 30;
    const POINT_MODE_CARD_VISIT_REWARD_ATTACHED = '名片访问奖励';

    const POINT_MODE_CARD_REGISTER_REWARD = 31;
    const POINT_MODE_CARD_REGISTER_REWARD_ATTACHED = '名片新增会员奖励';

    const POINT_MODE_PRESENTATION = 32;
    const POINT_MODE_PRESENTATION_ATTACHED = '锁定下线奖励';

    const POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION = 33;
    const POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION_ATTACHED = '爱心值提现扣除';

    const POINT_MODE_FIGHT_GROUPS_TEAM_SUCCESS = 34;
    const POINT_MODE_FIGHT_GROUPS_TEAM_SUCCESS_ATTACHED = '拼团活动团长奖励';

    const POINT_MODE_DRAW_CHARGE_GET = 35;
    const POINT_MODE_DRAW_CHARGE_GRT_ATTACHED = '抽奖获得';

    const POINT_MODE_DRAW_CHARGE_DEDUCTION = 36;
    const POINT_MODE_DRAW_CHARGE_DEDUCTION_ATTACHED = '抽奖使用扣除';

    const POINT_MODE_DRAW_REWARD_GET = 37;
    const POINT_MODE_DRAW_REWARD_GRT_ATTACHED = '抽奖奖励';

    const POINT_MODE_CONVERT = 38;
    const POINT_MODE_CONVERT_ATTACHED = '兑换';

    const POINT_MODE_THIRD = 39;
    const POINT_MODE_THIRD_ATTACHED = '第三方变动';

    const POINT_MODE_CONSUMPTION_POINTS = 40;
    const POINT_MODE_CONSUMPTION_POINTS_ATTACHED = '消费积分充值奖励';

    const POINT = 0;

    public $point_data = array();

    public $member_point;

    protected $member;
    /*
     * $data = [
     *      'point_income_type' //失去还是获得 POINT_INCOME_GET OR POINT_INCOME_LOSE
     *      'point_mode' // 1、2、3、4、5 收入方式
     *      'member_id' //会员id
     *      'point' //获得or支出多少积分
     *      //'before_point' //获取or支出 之前 x积分
     *      //'after_point' //获得or支出 之后 x积分
     *      'remark'   //备注
     * ]
     * */

    public function __construct(array $point_data)
    {
        if (!isset($point_data['point'])) {
            return;
        }
        $this->point_data = $point_data;
        $this->point_data['point'] = round($this->point_data['point'], 2);
        //$member = Member::getMemberById($point_data['member_id']);

        $this->member = $this->getMemberModel();
        $this->member_point = $this->member->credit1;
    }


    private function getMemberModel()
    {
        $member_id = $this->point_data['member_id'];
        $memberModel = Member::uniacid()->where('uid', $member_id)->lockForUpdate()->first();

        return $memberModel;
    }


    /**
     * Update member credit1.
     *
     * @return PointLog|bool
     * @throws ShopException
     */

    public function changePoint()
    {
        $point = floor($this->point_data['point'] * 100) / 100;
        if ($this->point_data['point_income_type'] == self::POINT_INCOME_LOSE) {
            $point = floor(abs($this->point_data['point']) * 100) / 100;
        }
        if ($point < 0.01) {
            return false;
        }
        $this->getAfterPoint();
        Member::updateMemberInfoById(['credit1' => $this->member_point], $this->point_data['member_id']);
        return $this->addLog();
    }

    public function addLog()
    {
        //$this->point_data['uniacid'] = \YunShop::app()->uniacid;
        $uniacid = \YunShop::app()->uniacid;
        $this->point_data['thirdStatus'] = empty($this->point_data['thirdStatus']) ? 1 : $this->point_data['thirdStatus'];
        $this->point_data['uniacid'] = !empty($uniacid) ? $uniacid : $this->point_data['uniacid'];
        $point_model = PointLog::create($this->point_data);
        if (!isset($point_model)) {
            return false;
        }
        $this->messageNotice();
        $this->checkFloorNotice();
        return $point_model;
    }

    public function messageNotice()
    {
        if ($this->point_data['point'] == 0) {
            return;
        }
        $template_id = \Setting::get('shop.notice')['point_change'];

        if(!$template_id){
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $this->member['nickname']],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '积分变动金额', 'value' => $this->point_data['point']],
            ['name' => '积分变动类型', 'value' => $this->getModeAttribute($this->point_data['point_mode'])],
            ['name' => '变动后积分数值', 'value' => $this->point_data['after_point']]
        ];
        $news_link = MessageTemp::find($template_id)->news_link;
        $news_link = $news_link ?:'';
        event(new MessageEvent($this->member->uid, $template_id, $params, $url=$news_link));
    }

    /**
     * 检测是否超过设置的下限并发送消息通知
     * @return bool
     */
    public function checkFloorNotice()
    {
        try{
            if ($this->point_data['point'] == 0) {
                return true;
            }


            $template_id = \Setting::get('shop.notice')['point_deficiency'];

            if(!$template_id){
                return true;
            }

            $set = Setting::get('point.set');
            if(!$set['point_floor']){
                return true;
            }

            if($set['point_floor_on'] == 0 || empty($set['point_message_type']) == true || in_array($set['point_message_type'],[1,2,3]) != true){
               return true;
            }


            //指定会员分组
            if($set['point_message_type'] == 3){
                if($this->member->yzMember->group_id != $set['group_type']){
                    return true;
                }
            }

            //指定会员等级
            if($set['point_message_type'] == 2){
                //这个会员属于当前的这个等级
                if($this->member->yzMember->level_id != $set['level_limit']){
                    return true;
                }
            }

            //指定会员
            if($set['point_message_type'] == 1){
                if(in_array($this->member->uid,explode(',',$set['uids'])) != true) {
                    return true;
                }
            }

            if($this->point_data['after_point'] > $set['point_floor']){
                $params = [
                    ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                    ['name' => '昵称', 'value' => $this->member['nickname']],
                    ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
                    ['name' => '通知额度', 'value' => $set['point_floor']],
                    ['name' => '当前积分', 'value' => $this->point_data['after_point']],
                ];
                $news_link = MessageTemp::find($template_id)->news_link;
                $news_link = $news_link ?:'';
                event(new MessageEvent($this->member->uid, $template_id, $params, $url=$news_link));
            }else{
                return true;
            }
        }catch (\Exception $e){
            \Log::debug('抛异常了');
            return true;
        }
    }

    /**
     * 获取变化之后的积分
     *
     * @throws ShopException
     */
    public function getAfterPoint()
    {
        $this->point_data['before_point'] = $this->member_point;
        $this->member_point += $this->point_data['point'];
        if ($this->member_point < PointService::POINT) {
            throw new ShopException('积分不足!!!');
            //$this->member_point = PointService::POINT;
        }
        $this->point_data['after_point'] = round($this->member_point, 2);
    }

    public function getModeAttribute($mode)
    {
        $mode_attribute = '';
        switch ($mode) {
            case (1):
                $mode_attribute = self::POINT_MODE_GOODS_ATTACHED;
                break;
            case (2):
                $mode_attribute = self::POINT_MODE_ORDER_ATTACHED;
                break;
            case (3):
                $mode_attribute = self::POINT_MODE_POSTER_ATTACHED;
                break;
            case (4):
                $mode_attribute = self::POINT_MODE_ARTICLE_ATTACHED;
                break;
            case (5):
                $mode_attribute = self::POINT_MODE_ADMIN_ATTACHED;
                break;
            case (6):
                $mode_attribute = self::POINT_MODE_BY_ATTACHED;
                break;
            case (7):
                $mode_attribute = self::POINT_MODE_TEAM_ATTACHED;
                break;
            case (8):
                $mode_attribute = self::POINT_MODE_LIVE_ATTACHED;
                break;
            case (9):
                $mode_attribute = self::POINT_MODE_CASHIER_ATTACHED;
                break;
            case (10):
                $mode_attribute = self::POINT_MODE_AIR_ATTACHED;
                break;
            case (11):
                $mode_attribute = self::POINT_MODE_RECHARGE_ATTACHED;
                break;
            case (12):
                $mode_attribute = self::POINT_MODE_FlOW_ATTACHED;
                break;
            case (13):
                $mode_attribute = self::POINT_MODE_TRANSFER_ATTACHED;
                break;
            case (14):
                $mode_attribute = self::POINT_MODE_RECIPIENT_ATTACHED;
                break;
            case (15):
                $mode_attribute = self::POINT_MODE_ROLLBACK_ATTACHED;
                break;
            case (16):
                $mode_attribute = self::POINT_MODE_COUPON_DEDUCTION_AWARD_ATTACHED;
                break;
            case (17):
                $mode_attribute = self::POINT_MODE_TASK_REWARD_ATTACHED;
                break;
            case (18):
                $mode_attribute = self::POINT_MODE_TRANSFER_LOVE_ATTACHED;
                break;
            case (19):
                $mode_attribute = trans('Yunshop\Sign::sign.plugin_name') ? trans('Yunshop\Sign::sign.plugin_name').'奖励' : self::POINT_MODE_SIGN_REWARD_ATTACHED;
                break;
            case (20):
                $mode_attribute = self::POINT_MODE_COURIER_REWARD_ATTACHED;
                break;
            case (22):
                $mode_attribute = self::POINT_MODE_COMMUNITY_REWARD_ATTACHED;
                break;
            case (23):
                $mode_attribute = self::POINT_MODE_CREATE_ACTIVITY_ATTACHED;
                break;
            case (24):
                $mode_attribute = self::POINT_MODE_ACTIVITY_OVERDUE_ATTACHED;
                break;
            case (25):
                $mode_attribute = self::POINT_MODE_RECEIVE_ACTIVITY_ATTACHED;
                break;
            case (26):
                $mode_attribute = self::POINT_MODE_RECEIVE_OVERDUE_ATTACHED;
                break;
            case (27):
                $mode_attribute = self::POINT_MODE_COMMISSION_TRANSFER_ATTACHED;
                break;
            case (28):
                $mode_attribute = self::POINT_MODE_HOTEL_CASHIER_ATTACHED;
                break;
            case (29):
                $mode_attribute = self::POINT_MODE_EXCEL_RECHARGE_ATTACHED;
                break;
            case (92):
                $mode_attribute = self::POINT_MODE_RECHARGE_CODE_ATTACHED;
                break;
            case (93):
                $mode_attribute = self::POINT_MODE_STORE_ATTACHED;
                break;
            case (94):
                $mode_attribute = self::POINT_MODE_HOTEL_ATTACHED;
                break;
            case (30):
                $mode_attribute = self::POINT_MODE_CARD_VISIT_REWARD_ATTACHED;
                break;
            case (31):
                $mode_attribute = self::POINT_MODE_CARD_REGISTER_REWARD_ATTACHED;
                break;
            case (32):
                $mode_attribute = self::POINT_MODE_PRESENTATION_ATTACHED;
                break;
            case (33):
                if(app('plugins')->isEnabled('love')){
                    $mode_attribute = \Yunshop\Love\Common\Services\SetService::getLoveName() ? \Yunshop\Love\Common\Services\SetService::getLoveName().'提现扣除' : self::POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION_ATTACHED;
                }else {
                    $mode_attribute = self::POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION_ATTACHED;
                }
                break;
            case (34):
                $mode_attribute = self::POINT_MODE_FIGHT_GROUPS_TEAM_SUCCESS_ATTACHED;
                break;
            case (35):
                $mode_attribute = self::POINT_MODE_DRAW_CHARGE_GRT_ATTACHED;
                break;
            case (36):
                $mode_attribute = self::POINT_MODE_DRAW_CHARGE_DEDUCTION_ATTACHED;
                break;
            case (37):
                $mode_attribute = self::POINT_MODE_DRAW_REWARD_GRT_ATTACHED;
                break;
            case (38):
                $mode_attribute = self::POINT_MODE_CONVERT_ATTACHED;
                break;
            case (39):
                $mode_attribute = self::POINT_MODE_THIRD_ATTACHED;
                break;
            case (40):
                $mode_attribute = self::POINT_MODE_CONSUMPTION_POINTS_ATTACHED;
                break;
        }
        return $mode_attribute;
    }
}