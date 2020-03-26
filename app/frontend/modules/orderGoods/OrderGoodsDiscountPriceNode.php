<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 3:01 PM
 */

namespace app\frontend\modules\orderGoods;

use app\common\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\order\PriceNode;
use app\frontend\modules\orderGoods\discount\BaseDiscount;
use app\frontend\modules\orderGoods\price\option\BaseOrderGoodsPrice;

class OrderGoodsDiscountPriceNode extends BaseOrderGoodsPriceNode
{
    /**
     * @var BaseDiscount
     */
    private $discount;

    public function __construct(BaseOrderGoodsPrice $orderGoodsPrice, BaseDiscount $discount, $weight)
    {
        $this->discount = $discount;
        parent::__construct($orderGoodsPrice, $weight);
    }

    function getKey()
    {
        return $this->discount->getCode();
    }

    /**
     * @return float|int|mixed
     * @throws \app\common\exceptions\AppException
     */
    function getPrice()
    {
        return $this->orderGoodsPrice->getPriceBefore($this->getKey()) - $this->discount->getAmount();
    }

}