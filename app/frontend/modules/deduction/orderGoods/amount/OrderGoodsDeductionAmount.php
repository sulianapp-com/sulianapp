<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午5:10
 */

namespace app\frontend\modules\deduction\orderGoods\amount;


use app\frontend\modules\deduction\GoodsDeduction;
use app\common\modules\orderGoods\models\PreOrderGoods;

/**
 * 订单商品抵扣金额基类
 * Class OrderGoodsDeductionAmount
 * @package app\frontend\modules\deduction\orderGoods\amount
 */
abstract class OrderGoodsDeductionAmount
{
    /**
     * 订单商品
     * @var PreOrderGoods
     */
    protected $orderGoods;
    /**
     * 商品抵扣设置
     * @var GoodsDeduction
     */
    protected $goodsDeduction;

    function __construct(PreOrderGoods $orderGoods, GoodsDeduction $goodsDeduction)
    {
        $this->orderGoods = $orderGoods;
        $this->goodsDeduction = $goodsDeduction;
    }

    /**
     * @return GoodsDeduction
     */
    protected function getGoodsDeduction()
    {
        return $this->goodsDeduction;
    }

    /**
     * @return PreOrderGoods
     */
    protected function getOrderGoods()
    {
        return $this->orderGoods;
    }

    /**
     * 最大抵扣金额
     * @return float
     */
    abstract public function getMaxAmount();

    /**
     * 最少抵扣金额
     * @return mixed
     */
    abstract public function getMinAmount();
}