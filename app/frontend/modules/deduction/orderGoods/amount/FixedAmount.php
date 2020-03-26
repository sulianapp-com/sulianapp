<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午5:07
 */

namespace app\frontend\modules\deduction\orderGoods\amount;

/**
 * 固定金额抵扣
 * Class FixedAmount
 * @package app\frontend\modules\deduction\orderGoods\amount
 */
class FixedAmount extends OrderGoodsDeductionAmount
{
    /**
     * @return float|mixed
     * @throws \app\common\exceptions\ShopException
     */
    public function getMaxAmount()
    {
        $result = $this->getGoodsDeduction()->getMaxFixedAmount() * $this->getOrderGoods()->total;
        $result = min($result,$this->getOrderGoods()->getPriceBeforeWeight($this->getGoodsDeduction()->getCode().'RestDeduction'));
        return max($result, 0);
    }

    /**
     * @return float|mixed
     * @throws \app\common\exceptions\ShopException
     */
    public function getMinAmount()
    {
        $result = $this->getGoodsDeduction()->getMinFixedAmount() * $this->getOrderGoods()->total;
        $result = min($result,$this->getOrderGoods()->getPriceBeforeWeight($this->getGoodsDeduction()->getCode().'MinDeduction'));

        return max($result, 0);
    }
}