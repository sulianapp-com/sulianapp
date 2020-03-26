<?php

namespace app\common\models;

use app\common\models\BaseModel;

class CouponLog extends BaseModel
{
    public $table = 'yz_coupon_log';
    public $guarded = [];
    public $timestamps = false;

    //多对一关系
    public function coupon()
    {
        return $this->belongsTo('app\common\models\Coupon', 'couponid', 'id');
    }

    //多对一关系
    public function member()
    {
        return $this->belongsTo('app\common\models\Member', 'member_id', 'uid');
    }
}