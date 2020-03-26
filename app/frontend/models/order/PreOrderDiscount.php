<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\models\order;

use app\frontend\models\Order;
use app\frontend\modules\order\models\PreOrder;
use app\common\models\order\OrderDiscount;

class PreOrderDiscount extends OrderDiscount
{
    /**
     * @var Order
     */
    public $order;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function setOrder(PreOrder $order)
    {
        $this->order = $order;
        $this->uid = $order->uid;
        $order->orderDiscounts->push($this);
    }
}