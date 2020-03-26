<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/22
 * Time: 3:22 PM
 */

namespace app\frontend\modules\order;

class OrderGoodsPriceNode extends OrderPriceNode
{

    public function getKey()
    {
        return 'orderGoodsPrice';
    }

    public function getPrice()
    {
        return $this->order->getOrderGoodsPrice();
    }
}