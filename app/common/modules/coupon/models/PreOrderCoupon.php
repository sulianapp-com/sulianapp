<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/25
 * Time: 9:15 PM
 */

namespace app\common\modules\coupon\models;

use app\common\models\Coupon;
use app\common\models\Order;
use app\common\models\order\OrderCoupon;

class PreOrderCoupon extends OrderCoupon
{

    /**
     * @var
     */
    private $amoutCalculator;

    /**
     * @var
     */
    private $useScope;

    /**
     * @var
     */
    private $timeLimit;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Coupon
     */
    private $coupon;

    public function init(Order $order, Coupon $coupon)
    {
        $this->order = $order;
        $this->coupon = $coupon;
        $this->initAttributes();
    }

    protected function initAttributes()
    {
        [
            'amount' => $this->getAmount(),
            'order_id' => $this->order->id,
            'coupon_id' => $this->coupon->id,
            'amount' => $this->getAmount(),
        ];
    }

    public function getAmount()
    {
        return 100;
    }

}