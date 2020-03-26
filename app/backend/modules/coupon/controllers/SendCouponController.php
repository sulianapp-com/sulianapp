<?php

namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;
use app\common\exceptions\ShopException;
use app\common\models\MemberCoupon;
use app\common\models\McMappingFans;
use app\common\models\Member;
use app\common\models\Coupon;
use app\common\models\CouponLog;
use app\backend\modules\coupon\services\Message;
use app\common\models\MemberShopInfo;
use app\backend\modules\coupon\services\MessageNotice;


class SendCouponController extends BaseController
{
    const BY_MEMBERIDS = 1;
    const BY_MEMBER_LEVEL = 2;
    const BY_MEMBER_GROUP = 3;
    const TO_ALL_MEMBERS = 4;

    public $failedSend = []; //发送失败时的记录
    public $adminId; //后台操作者的ID

    public function index()
    {
        $couponId = \YunShop::request()->id;
        $couponModel = Coupon::getCouponById($couponId);

        //获取会员等级列表
        $memberLevels = MemberLevel::getMemberLevelList();

        //获取会员分组列表
        $memberGroups = MemberGroup::getMemberGroupList();

        if ($_POST) {

            //获取后台操作者的ID
            $this->adminId = \YunShop::app()->uid;

            //获取会员 Member ID
            $sendType = \YunShop::request()->sendtype;
            switch ($sendType) {
                case self::BY_MEMBERIDS:
                    $membersScope = trim(\YunShop::request()->send_memberid);
                    $patternMatchNumArray = preg_match('/(\d+,)+(\d+,?)/', $membersScope); //匹配比如 "2,3,78"或者"2,3,78,"
                    $patternMatchSingleNum = preg_match('/(\d+)(,)?/', $membersScope); //匹配单个数字

                    $memberIds = explode(',', $membersScope);
                    $uid = Member::getMemberId($memberIds);//提取该搜索公众号下的会员id
                    $uids = [];

                    foreach ($uid as $key=>$item){
                        $uids[$key] = $item['uid'];//将查出的会员id装到数组里
                    }

                    $member_ids = collect($memberIds)->map(function ($item) {//循环转换为数值类型
                        return intval($item);
                    })->toArray();

//                    dd($member_ids);
                    $arr = array_diff($member_ids,$uids);//提交过来的会员id与查询出来的会员id对比，留下不存在该公众号的会员id

                    if (!empty($arr)){  //判断是否存在不是该公众号的会员id
                         throw new ShopException("发放优惠券失败，请确认该".implode(",", $arr)."会员是否是该公众号会员");
                    }

                    if ($patternMatchNumArray || $patternMatchSingleNum) {
                        $patternMatch = true;
                    } else {
                        $patternMatch = false;
                    }
                    break;
                case self::BY_MEMBER_LEVEL: //根据"会员等级"获取 Member IDs
                    $sendLevel = \YunShop::request()->send_level;
                    if (!$sendLevel) {
                        return $this->message('请选择会员等级！', '', 'error');
                    }
                    $res = MemberLevel::getMembersByLevel($sendLevel);
                    if ($res['member']->isEmpty()) {
                        $memberIds = '';
                    } else {
                        $res = $res->toArray();
                        $memberIds = array_column($res['member'], 'member_id'); //提取member_id组成新的数组
                    }
                    break;
                case self::BY_MEMBER_GROUP: //根据"会员分组"获取 Member IDs
                    $sendGroup = \YunShop::request()->send_group;
                    if (!$sendGroup) {
                        return $this->message('请选择会员组！', '', 'error');
                    }
                    $res = MemberGroup::getMembersByGroupId($sendGroup);
                    if ($res['member']->isEmpty()) {
                        $memberIds = '';
                    } else {
                        $res = $res->toArray();
                        $memberIds = array_column($res['member'], 'member_id'); //提取member_id组成新的数组
                    }
                    break;
                case self::TO_ALL_MEMBERS:
//                    $res = Member::getMembersId();
                    $res = MemberShopInfo::getYzMembersId();
                    if (!$res) {
                        $members = '';
                    } else {
                        $members = $res->toArray();
                    }
                    $memberIds = array_column($members, 'member_id');
                    break;
                default:
                    $memberIds = '';
            }

            //获取发放的数量
            $sendTotal = \YunShop::request()->send_total;
            $getTotal = MemberCoupon::uniacid()->where("coupon_id", $couponModel->id)->count();
            $lastTotal = $couponModel->total - $getTotal;
            if (empty($memberIds)) {
                throw new ShopException('该发放类型下还没有用户');
            }elseif(!$couponModel->status){
                throw new ShopException('优惠券已下架,请先重新上架');
            } elseif ($sendTotal < 1) {
                throw new ShopException('发放数量必须为整数, 而且不能小于 1');
            } elseif (isset($patternMatch) && !$patternMatch) {
                throw new ShopException('Member ID 填写不正确, 请重新设置');
            } elseif (($couponModel->total != -1) && ($sendTotal * count($memberIds) > $lastTotal)) {
                // 优惠券有限,并且发放数量超过限制
                if($lastTotal<0){
                    throw new ShopException("剩余优惠券不足(准备发放".$sendTotal * count($memberIds)."张,此前已超发".abs($lastTotal)."张)");
                }
                throw new ShopException("剩余优惠券不足(准备发放".$sendTotal * count($memberIds)."张,剩余{$lastTotal}张)");
            } else {

                //发放优惠券
                $responseData = [
                    'title' => htmlspecialchars_decode($couponModel->resp_title),
                    'image' => tomedia($couponModel->resp_thumb),
                    'description' => $couponModel->resp_desc ? htmlspecialchars_decode($couponModel->resp_desc) : '亲爱的 [nickname], 你获得了 1 张 "' . $couponModel->name . '" 优惠券',
                    'url' => $couponModel->resp_url ?: yzAppFullUrl('home'),
                ];
                $res = $this->sendCoupon($couponModel, $memberIds, $sendTotal, $responseData);
                if ($res) {

                    //发送获取通知
                    foreach ($memberIds as $memberId) {
                        MessageNotice::couponNotice($couponModel->id,$memberId);
                    }

                    return $this->message('手动发送优惠券成功');
                } else {
                    return $this->message('有部分优惠券未能发送, 请检查数据库', '', 'error');
                }
            }
        }

        return view('coupon.send', [
            'send_total' => isset($sendTotal) ? $sendTotal : 0,
            'sendtype' => isset($sendType) ? $sendType : 1,
            'memberLevels' => $memberLevels, //用户等级列表
            'memberGroups' => $memberGroups, //用户分组列表
            'send_level' => isset($sendLevel) ? $sendLevel : 1,
            'memberGroupId' => isset($sendGroup) ? $sendGroup : 1,
            'agentLevelId' => isset($sendLevel) ? $sendLevel : 1,
        ])->render();
    }





    //发放优惠券
    //array $members
    public function sendCoupon($couponModel, $memberIds, $sendTotal, $responseData)
    {

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'coupon_id' => $couponModel->id,
            'get_type' => 0,
            'used' => 0,
            'get_time' => strtotime('now'),
        ];

        foreach ($memberIds as $memberId) {

            $messageData = $responseData;
//            //获取Openid
//            $memberOpenid = McMappingFans::getFansById($memberId)->openid;


            for ($i = 0; $i < $sendTotal; $i++) {
                $memberCoupon = new MemberCoupon;
                $data['uid'] = $memberId;
                $res = $memberCoupon->create($data);

                //写入log
                if ($res) { //发放优惠券成功
                    $log = '手动发放优惠券成功: 管理员( ID 为 ' . $this->adminId . ' )成功发放 ' . $sendTotal . ' 张优惠券( ID为 ' . $couponModel->id . ' )给用户( Member ID 为 ' . $memberId . ' )';

                } else { //发放优惠券失败
                    $log = '手动发放优惠券失败: 管理员( ID 为 ' . $this->adminId . ' )发放优惠券( ID为 ' . $couponModel->id . ' )给用户( Member ID 为 ' . $memberId . ' )时失败!';
                    $this->failedSend[] = $log; //失败时, 记录 todo 最后需要展示出来
                    \Log::info($log);
                }
                $this->log($log, $couponModel, $memberId);
            }

            if (!empty($messageData['title'])) { //没有关注公众号的用户是没有 openid
                $templateId = \Setting::get('coupon_template_id'); //模板消息ID
                $nickname = Member::getMemberById($memberId)->nickname;
                $dynamicData = [
                    'nickname' => $nickname,
                    'couponname' => $couponModel->name,
                ];
                $messageData['title'] = self::dynamicMsg($messageData['title'], $dynamicData);
                $messageData['description'] = self::dynamicMsg($messageData['description'], $dynamicData);

                Message::message($messageData, $templateId, $memberId); //默认使用微信"客服消息"通知, 对于超过 48 小时未和平台互动的用户, 使用"模板消息"通知
            }
        }

        if (empty($this->failedSend)) {
            return true;
        } else {
            return false;
        }
    }

    //写入日志
    public function log($log, $couponModel, $memberId)
    {
        $logData = [
            'uniacid' => \YunShop::app()->uniacid,
            'logno' => $log,
            'member_id' => $memberId,
            'couponid' => $couponModel->id,
            'paystatus' => 0, //todo 手动发放的不需要支付?
            'creditstatus' => 0, //todo 手动发放的不需要支付?
            'paytype' => 0, //todo 这个字段什么含义?
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        $res = CouponLog::create($logData);
        return $res;
    }

    //动态显示内容
    protected static function dynamicMsg($msg, $data)
    {
        if (preg_match('/\[nickname\]/', $msg)) {
            $msg = str_replace('[nickname]', $data['nickname'], $msg);
        }
        if (preg_match('/\[couponname\]/', $msg)) {
            $msg = str_replace('[couponname]', $data['couponname'], $msg);
        }
        return $msg;
    }
}
