<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/12
 * Time: 9:05
 */

namespace app\backend\modules\coupon\models;


use app\common\observers\coupon\CouponObserver;

class HotelCoupon extends Coupon
{
    protected static function boot()
    {
        if(app('plugins')->isEnabled('hotel')){
            static::observe(new CouponObserver());
        }
        parent::boot();
    }
}