<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\services\CouponService;

class CouponDiscount extends BaseDiscount
{
    protected $code = 'coupon';
    protected $name = '优惠券优惠';
    /**
     * 获取总金额
     * @return float
     */
    protected function _getAmount()
    {
        // 优先计算折扣类订单优惠券
        $discountCouponService = (new CouponService($this->order, Coupon::COUPON_DISCOUNT));
        $discountPrice = $discountCouponService->getOrderDiscountPrice();
        //$discountCouponService->activate();
        //dd($discountPrice);

        // 满减订单优惠券
        $moneyOffCouponService = (new CouponService($this->order, Coupon::COUPON_MONEY_OFF));
        $moneyOffPrice = $moneyOffCouponService->getOrderDiscountPrice();
        //dd($moneyOffPrice);
        //$moneyOffCouponService->activate();
        $result = $discountPrice + $moneyOffPrice;

        return $result;
    }
}