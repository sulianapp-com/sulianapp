<?php

namespace app\backend\modules\coupon\models;

use app\common\models\BaseModel;

// 订单关闭，订单中的优惠券返还给会员，该模型记录返还的order_coupon_id
class OrderCouponReturn extends BaseModel
{
    public $table = 'yz_order_coupon_return';
    public $timestamps = false;
    public $guarded = [];
}