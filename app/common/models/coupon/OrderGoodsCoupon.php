<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2019/12/17
 * Time: 10:52
 */

namespace app\common\models\coupon;


use app\common\models\BaseModel;
use app\common\models\OrderGoods;

class OrderGoodsCoupon extends BaseModel
{
    protected $table = 'yz_order_goods_coupon';

    protected $guarded = [''];

    const MONTH_TYPE = 0;
    const ORDER_TYPE = 1;

    const CLOSE_STATUS = -1;
    const WAIT_STATUS = 0;
    const SEND_STATUS = 1;

    public function hasOneOrderGoods()
    {
        return $this->hasOne(OrderGoods::class, 'id', 'order_goods_id');
    }
}