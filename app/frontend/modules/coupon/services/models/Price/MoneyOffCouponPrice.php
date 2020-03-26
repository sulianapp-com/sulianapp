<?php
/**
 * 立减优惠券
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:21
 */

namespace app\frontend\modules\coupon\services\models\Price;

use app\common\models\coupon\GoodsMemberCoupon;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

class MoneyOffCouponPrice extends CouponPrice
{
    /**
     * 优惠券价格
     * @return mixed
     */
    protected function _getAmount()
    {
        return min($this->dbCoupon->deduct, $this->getOrderGoodsCollectionPaymentAmount());
    }


}