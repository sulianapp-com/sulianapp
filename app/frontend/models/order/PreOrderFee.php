<?php

namespace app\frontend\models\order;

use app\common\models\order\OrderFee;
use app\frontend\models\Order;
use app\frontend\modules\order\models\PreOrder;

class PreOrderFee extends OrderFee
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

        $order->orderFees->push($this);
    }
}