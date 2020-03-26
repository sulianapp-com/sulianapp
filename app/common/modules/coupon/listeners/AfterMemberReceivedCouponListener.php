<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/28
 * Time: 12:06 AM
 */

namespace app\common\modules\coupon\listeners;

use app\common\models\CouponLog;
use app\backend\modules\coupon\services\MessageNotice;
use app\common\modules\coupon\events\AfterMemberReceivedCoupon;

class AfterMemberReceivedCouponListener
{
    public function handle(AfterMemberReceivedCoupon $event)
    {
        //推送模板消息通知用户
        $memberCoupon = $event->memberCoupon();

        //发送获取通知
        MessageNotice::couponNotice($memberCoupon->coupon_id, $memberCoupon->uid);

        //写入log
        $logData = [
            'uniacid' => \YunShop::app()->get('uniacid'),
            'logno' => '领取优惠券成功: 用户( ID 为 ' . $memberCoupon->uid . ' )成功领取 1 张优惠券( ID 为 ' . $memberCoupon->coupon_id . ' )',
            'member_id' => $memberCoupon->uid,
            'couponid' => $memberCoupon->coupon_id,
            'getfrom' => 1,
            'status' => 0,
            'createtime' => strtotime('now'),
        ];
        CouponLog::create($logData);
    }

}