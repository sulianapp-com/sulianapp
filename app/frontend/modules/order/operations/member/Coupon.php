<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29
 * Time: 15:37
 */

namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\coupon\models\ShoppingShareCoupon;
use app\frontend\modules\order\operations\OrderOperation;

class Coupon extends OrderOperation
{
    public function getApi()
    {
        return 'coupon.share-coupon.share';
    }

    public function getName()
    {
        return '分享';
    }

    public function getValue()
    {
        return static::COUPON;
    }

    public function enable()
    {
        if (ShoppingShareCoupon::where('order_id', $this->order->id)->first()) {
            return true;
        }
        return false;
    }
}