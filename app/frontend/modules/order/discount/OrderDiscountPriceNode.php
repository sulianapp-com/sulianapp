<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:40 AM
 */

namespace app\frontend\modules\order\discount;

use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\PriceNode;

class OrderDiscountPriceNode extends PriceNode
{
    private $discount;
    private $order;

    public function __construct(PreOrder $order, BaseDiscount $discount, $weight)
    {
        $this->order = $order;
        $this->discount = $discount;
        parent::__construct($weight);
    }

    function getKey()
    {
        return $this->discount->getCode();
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    function getPrice()
    {
        return max($this->order->getPriceBefore($this->getKey()) - $this->discount->getAmount(),0);
    }

}