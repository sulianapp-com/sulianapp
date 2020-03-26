<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;


use app\frontend\modules\order\models\PreOrder;

class PreOrderCoupon extends \app\common\models\order\OrderCoupon
{
    public $order;
    protected $hidden = ['memberCoupon'];
    public $coupon;

    public function setOrder(PreOrder $order)
    {
        $this->order = $order;
        $this->uid = $order->uid;
        $order->orderCoupons->push($this);
    }

    public function afterSaving()
    {
        $this->push();
    }

    public function save(array $options = [])
    {
        if (isset($this->id)) {
            return true;
        }
        return parent::save($options);
    }
}