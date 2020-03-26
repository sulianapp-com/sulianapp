<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 3:01 PM
 */

namespace app\frontend\modules\orderGoods;

use app\common\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;
use app\frontend\modules\order\PriceNode;
use app\frontend\modules\orderGoods\discount\BaseDiscount;
use app\frontend\modules\orderGoods\price\option\BaseOrderGoodsPrice;

class OrderGoodsDeductionPriceNode extends BaseOrderGoodsPriceNode
{
    /**
     * @var PreOrderGoodsDeduction
     */
    private $preOrderGoodsDeduction;

    public function __construct(BaseOrderGoodsPrice $orderGoodsPrice, PreOrderGoodsDeduction $preOrderGoodsDeduction, $weight)
    {
        $this->preOrderGoodsDeduction = $preOrderGoodsDeduction;
        parent::__construct($orderGoodsPrice, $weight);
    }

    function getKey()
    {
        return $this->preOrderGoodsDeduction->code.'Deduction';
    }

    /**
     * @return float|int|mixed
     * @throws \app\common\exceptions\AppException
     */
    function getPrice()
    {
        return $this->orderGoodsPrice->getPriceBefore($this->getKey()) - $this->preOrderGoodsDeduction->used_amount;
    }

}