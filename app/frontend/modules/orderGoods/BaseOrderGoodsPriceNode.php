<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 3:01 PM
 */

namespace app\frontend\modules\orderGoods;

use app\frontend\modules\order\PriceNode;
use app\frontend\modules\orderGoods\price\option\BaseOrderGoodsPrice;

abstract class BaseOrderGoodsPriceNode extends PriceNode
{
    /**
     * @var BaseOrderGoodsPrice
     */
    protected $orderGoodsPrice;
    public function __construct(BaseOrderGoodsPrice $orderGoodsPrice, $weight)
    {
        $this->orderGoodsPrice = $orderGoodsPrice;
        parent::__construct($weight);
    }
}