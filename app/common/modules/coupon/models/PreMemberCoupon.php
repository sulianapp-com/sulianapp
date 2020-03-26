<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/27
 * Time: 10:57 PM
 */

namespace app\common\modules\coupon\models;

use app\common\exceptions\AppException;
use app\common\models\Coupon;
use app\common\models\Member;
use app\common\models\MemberCoupon;
use app\common\models\MemberShopInfo;
use app\common\modules\coupon\events\AfterMemberReceivedCoupon;

class PreMemberCoupon extends MemberCoupon
{
    /**
     * @var Member $member
     */
    private $member;
    /**
     * @var Coupon $coupon
     */
    private $coupon;

    /**
     * @param Member $member
     * @param Coupon $coupon
     */
    public function init(Member $member, Coupon $coupon)
    {
        $this->member = $member;
        $this->coupon = $coupon;
        $this->initAttributes();
    }

    private function initAttributes()
    {
        $data = [
            'uniacid' => $this->member->uniacid,
            'uid' => $this->member->uid,
            'coupon_id' => $this->coupon->id,
            'get_type' => 1,
            'get_time' => time(),
        ];
        $this->fill($data);
    }

    /**
     * @throws AppException
     */
    public function generate()
    {
        $this->verify($this->member->yzMember, $this->coupon);
        $validator = $this->validator();
        if ($validator->fails()) {
            throw new AppException('领取失败', $validator->messages());
        }
        $this->save();
        event(new AfterMemberReceivedCoupon($this));
    }

    /**
     * @param MemberShopInfo $yzMember
     * @param Coupon $coupon
     * @throws AppException
     */
    public function verify(MemberShopInfo $yzMember, Coupon $coupon)
    {
        if (!$coupon->available()) {
            throw new AppException('没有该优惠券或者优惠券不可用');
        }
        if (!empty($coupon->level_limit) && ($coupon->level_limit != -1)) { //优惠券有会员等级要求
            // 通过会员记录的level_id找到会员等级
            $memberLevel = \app\common\models\MemberLevel::find($yzMember->level_id)->level;
            // 通过优惠券记录的level_id找到会员等级,level_limit实际就是level_id
            $couponMemberLevel = \app\common\models\MemberLevel::find($coupon->level_limit)->level;
            if (empty($yzMember->level_id)) {
                throw new AppException('该优惠券有会员等级要求,但该用户没有会员等级');
            } elseif ((!empty($memberLevel) ? $memberLevel : 0) < $couponMemberLevel) {
                throw new AppException('没有达到领取该优惠券的会员等级要求');
            }
        }

        //判断优惠券是否过期
        $timeLimit = $coupon->time_limit;

        if ($timeLimit == 1 && (time() > $coupon->time_end->timestamp)) {
            throw new AppException('优惠券已过期');

        }

        //是否达到个人领取上限
        $count = self::where('uid', $yzMember->member_id)->where('coupon_id', $coupon->id)->count();
        if ($count >= $coupon->get_max && ($coupon->get_max != -1)) {
            throw new AppException('已经达到个人领取上限');
        }

        //验证是否达到优惠券总数上限
        if ($coupon->getReceiveCount() >= $coupon->total && ($coupon->total != -1)) {
            throw new AppException('该优惠券已经被抢光');
        }
    }
}