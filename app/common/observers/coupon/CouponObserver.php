<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/2
 * Time: 10:39
 */

namespace app\common\observers\coupon;

use app\common\models\Coupon;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;
use Yunshop\Hotel\common\models\CouponHotel;

class CouponObserver extends BaseObserver
{
    public function created(Model $model)
    {
        if($model->widgets['more_hotels'] && $model->use_type == Coupon::COUPON_MORE_HOTEL_USE){
            $arr = $model->widgets['more_hotels'];
            foreach ($arr as $v){
                $couponHotel = new CouponHotel();
                $couponHotel->fill([
                    'coupon_id' => $model->id,
                    'hotel_id' => $v
                ]);
                $couponHotel->save();
            }
        }
    }


    public function updated(Model $model)
    {
            CouponHotel::where([
                'coupon_id' => $model->id,
            ])->delete();
        if($model->widgets['more_hotels'] && $model->use_type == Coupon::COUPON_MORE_HOTEL_USE){
            foreach ($model->widgets['more_hotels'] as $v){
                $couponHotel = new CouponHotel();
                $couponHotel->fill([
                    'coupon_id' => $model->id,
                    'hotel_id' => $v
                ]);
                $couponHotel->save();
            }
        }
    }


    public function deleted(Model $model)
    {
        CouponHotel::where([
            'coupon_id' => $model->id,
        ])->delete();
    }
}