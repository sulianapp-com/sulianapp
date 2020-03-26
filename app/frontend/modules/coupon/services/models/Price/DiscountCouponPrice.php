<?php
/**
 * 折扣优惠券
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:20
 */

namespace app\frontend\modules\coupon\services\models\Price;


use app\common\models\coupon\GoodsMemberCoupon;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

class DiscountCouponPrice extends CouponPrice
{
    protected function _getAmount()
    {
        return (1 - $this->dbCoupon->discount/10) * $this->getOrderGoodsCollectionPaymentAmount();
    }
}