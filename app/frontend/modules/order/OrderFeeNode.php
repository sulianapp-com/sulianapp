<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/22
 * Time: 3:22 PM
 */

namespace app\frontend\modules\order;

class OrderFeeNode extends OrderPriceNode
{

    public function getKey()
    {
        return 'orderFee';
    }

    public function getPrice()
    {
        return $this->order->getPriceBefore($this->getKey()) + $this->order->getOrderFeeManager()->getAmount();
    }
}