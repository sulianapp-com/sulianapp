<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午1:48
 */

namespace app\frontend\modules\coupon\services\models;


use app\common\helpers\Serializer;
use app\common\models\MemberCoupon;
use app\common\models\Coupon as DbCoupon;

use app\frontend\models\order\PreOrderCoupon;
use app\frontend\modules\coupon\services\MemberCouponService;
use app\frontend\modules\coupon\services\models\Price\CouponPrice;
use app\frontend\modules\coupon\services\models\Price\DiscountCouponPrice;
use app\frontend\modules\coupon\services\models\Price\MoneyOffCouponPrice;
use app\frontend\modules\coupon\services\models\TimeLimit\DateTimeRange;
use app\frontend\modules\coupon\services\models\TimeLimit\SinceReceive;
use app\frontend\modules\coupon\services\models\TimeLimit\TimeLimit;
use app\frontend\modules\coupon\services\models\UseScope\CategoryScope;
use app\frontend\modules\coupon\services\models\UseScope\CouponUseScope;
use app\frontend\modules\coupon\services\models\UseScope\GoodsScope;
use app\frontend\modules\coupon\services\models\UseScope\ShopScope;
use app\frontend\modules\order\models\PreOrder;
use phpDocumentor\Reflection\Types\Null_;

class Coupon
{
    /**
     * @var CouponPrice
     */
    private $price;
    /**
     * @var CouponUseScope
     */
    private $useScope;
    /**
     * @var TimeLimit
     */
    private $timeLimit;

    /**
     * @var PreOrder
     */
    private $order;
    /**
     * @var \app\common\models\MemberCoupon
     */
    private $memberCoupon;

    public function __construct(MemberCoupon $memberCoupon, PreOrder $order)
    {
        $this->memberCoupon = $memberCoupon;
        $this->order = $order;
        $this->price = $this->getPriceInstance();
        $this->useScope = $this->getUseScopeInstance();
        $this->timeLimit = $this->getTimeLimitInstance();
    }

    public function getPreOrder()
    {
        return $this->order;
    }

    /**
     * @return MemberCoupon
     */
    public function getMemberCoupon()
    {
        return $this->memberCoupon;
    }

    /**
     * 金额类的实例
     */
    private function getPriceInstance()
    {
        switch ($this->memberCoupon->belongsToCoupon->coupon_method) {
            case DbCoupon::COUPON_MONEY_OFF:
                return new MoneyOffCouponPrice($this);
                break;
            case DbCoupon::COUPON_DISCOUNT:
                return new DiscountCouponPrice($this);
                break;
            default:
//                if (config('app.debug')) {
//                    dd($this->memberCoupon->belongsToCoupon->coupon_method);
//                    dd($this->memberCoupon);
//                    throw new AppException('优惠券优惠类型不存在');
//                }
                return null;
                break;
        }
    }

    /**
     * 使用范围类的实例,从config中读取值与类的对应关系,以便插件扩展
     */
    private function getUseScopeInstance()
    {
        $item = array_first(\app\common\modules\shop\ShopConfig::current()->get('shop-foundation.coupon.OrderCoupon.scope'), function ($item) {
            if($this->memberCoupon->belongsToCoupon->use_type == 8){
                return $item['key'] == 2;
            }
            return $item['key'] == $this->memberCoupon->belongsToCoupon->use_type;
        }, null);

        if (isset($item) && $item['class'] instanceof \Closure) {
            return call_user_func($item['class'], $this);
        }

        return null;
    }

    /**
     * 时间限制类实例
     */
    private function getTimeLimitInstance()
    {
        switch ($this->memberCoupon->belongsToCoupon->time_limit) {
            case DbCoupon::COUPON_DATE_TIME_RANGE:
                return new DateTimeRange($this);
                break;
            case DbCoupon::COUPON_SINCE_RECEIVE:
                return new SinceReceive($this);
                break;
            default:
//                if (config('app.debug')) {
//                    dd($this->memberCoupon->belongsToCoupon);
//                    throw new AppException('时限类型不存在');
//                }

                return null;
                break;
        }
    }

    /**
     * 获取订单优惠价格
     */
    public function getDiscountAmount()
    {
        $this->setOrderGoodsDiscountPrice();

        return $this->price->getPrice();
    }

    /**
     * 激活优惠券
     */
    public function activate()
    {
        if ($this->getMemberCoupon()->selected) {
            return;
        }
        //记录优惠券被选中了
        $this->getMemberCoupon()->selected = 1;
        $this->getMemberCoupon()->used = 1;
        $this->getMemberCoupon()->use_time = time();
        //dump($this->getMemberCoupon());coupo

        // todo 订单优惠券使用记录暂时加在这里,优惠券部分需要重构
        $preOrderCoupon = new PreOrderCoupon([
            'coupon_id' => $this->memberCoupon->coupon_id,
            'member_coupon_id' => $this->memberCoupon->id,
            'name' => $this->memberCoupon->belongsToCoupon->name,
            'amount' => $this->getDiscountAmount()
        ]);
        $preOrderCoupon->setRelation('memberCoupon', $this->memberCoupon);
        $preOrderCoupon->coupon = $this;
        $preOrderCoupon->setOrder($this->order);

    }

    /**
     * 分配优惠金额 todo 需理清与订单商品类之间的调用关系
     */
    private function setOrderGoodsDiscountPrice()
    {
        $this->price->setOrderGoodsDiscountPrice();
    }

    /**
     * 获取范围内的订单商品
     */
    public function getOrderGoodsInScope()
    {
        return $this->useScope->getOrderGoodsInScope();
    }

    /**
     * 优惠券可使用
     * @return bool
     */
    public function valid()
    {
        if (!$this->isOptional()) {

            return false;
        }
        if (!$this->unique()) {
            return false;
        }
        if (!$this->price->valid()) {

            return false;
        }
        return true;
    }

    /**
     * 用户优惠券所属的优惠券未被选中过
     * @return bool
     */
    public function unique()
    {
        //允许多张使用
        if ($this->getMemberCoupon()->belongsToCoupon->is_complex) {
            return true;
        }
        $memberCoupons = MemberCouponService::getCurrentMemberCouponCache($this->getPreOrder()->belongsToMember);
        //本优惠券与某个选中的优惠券是一张 就返回false
        return !$memberCoupons->contains(function ($memberCoupon) {

            if ($memberCoupon->selected == true) {
                //本优惠券与选中的优惠券是一张
                trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '同一单不能使用多张此类型优惠券');

                return $memberCoupon->coupon_id == $this->getMemberCoupon()->coupon_id;
            }
            return false;
        });

    }

    /**
     * 优惠券已选中
     * @return bool
     */
    public function isChecked()
    {

        if ($this->getMemberCoupon()->selected == 1) {
            return true;
        }
        return false;
    }

    /**
     * 优惠券可选
     * @return bool
     */
    public function isOptional()
    {
        //检测优惠券是否被删除
        if($this->memberCoupon->is_member_deleted == 1){
            return false;
        }

//        dd($this->useScope);
        if (!isset($this->useScope)) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '范围设置无效');
            return false;
        }
        if (!isset($this->price)) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '满减类型设置无效');
            return false;
        }
        if (!isset($this->timeLimit)) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '时限类型设置无效');

            return false;
        }
        //满足范围
        if (!$this->useScope->valid()) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '不满足范围');
            return false;
        }
        //满足额度
        if (!$this->price->isOptional()) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '不满足额度');
            return false;
        }
        //满足时限
        if (!$this->timeLimit->valid()) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '不满足时限');
            return false;
        }
        //未使用
        if ($this->getMemberCoupon()->used) {
            trace_log()->coupon("优惠券{$this->getMemberCoupon()->id}", '已使用');
            return false;
        }

        return true;
    }

}