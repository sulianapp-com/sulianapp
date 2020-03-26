<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:12
 */

namespace app\frontend\modules\deduction;

use app\common\models\VirtualCoin;
use app\common\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\coin\InvalidVirtualCoin;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;
use Illuminate\Database\Eloquent\Collection;

class OrderGoodsDeductionCollection extends Collection
{
    public function loadOrderGoodsDeductions(PreOrderGoods $orderGoods, \Illuminate\Database\Eloquent\Collection $deductions)
    {
        $this->items = $deductions->map(function (Deduction $deduction) use ($orderGoods) {
            $preOrderGoodsDeduction = new PreOrderGoodsDeduction([
                'name' => $deduction->getName(),
                'code' => $deduction->getCode()
            ]);
            $preOrderGoodsDeduction->setOrderGoods($orderGoods);
            return $preOrderGoodsDeduction;
        })->all();
        return $this;
    }

    /**
     * @return VirtualCoin
     */
    public function getMinPoint()
    {
        if($this->isEmpty()){
            return new InvalidVirtualCoin();
        }
        trace_log()->deduction('订单抵扣', "订单商品集合开始计算所有可用的虚拟币");
        $result = $this->reduce(function ($result, PreOrderGoodsDeduction $orderGoodsDeduction) {
            /**
             * @var PreOrderGoodsDeduction $orderGoodsDeduction
             */

            if (!isset($result)) {
                return $orderGoodsDeduction->getMinLimitBuyCoin();
            }

            return $orderGoodsDeduction->getMinLimitBuyCoin()->plus($result);
        });
        trace_log()->deduction('订单抵扣', "所有订单商品最低抵扣{$result->getMoney()}元");

        return $result;
    }

    /**
     * @return VirtualCoin
     */
    public function getUsablePoint()
    {
        if($this->isEmpty()){
            return new InvalidVirtualCoin();
        }
        trace_log()->deduction('订单抵扣', "开始订单商品集合计算所有可用的虚拟币");
        $result = $this->reduce(function ($result, PreOrderGoodsDeduction $orderGoodsDeduction) {
            /**
             * @var PreOrderGoodsDeduction $orderGoodsDeduction
             */

            if (!isset($result)) {
                return $orderGoodsDeduction->getUsableCoin();
            }
            return $orderGoodsDeduction->getUsableCoin()->plus($result);
        });
        trace_log()->deduction('订单抵扣', "完成订单商品集合计算所有可用{$result->getMoney()}元");

        return $result;
    }

    /**
     * 订单商品抵扣集合中 已使用的抵扣
     * @return VirtualCoin
     */
    public function getUsedPoint()
    {
        if($this->isEmpty()){
            return new InvalidVirtualCoin();
        }
        trace_log()->deduction('订单抵扣', "开始订单商品抵扣集合计算所有已用的虚拟币");

        $result = $this->reduce(function ($result, $orderGoodsDeduction) {
            /**
             * @var PreOrderGoodsDeduction $orderGoodsDeduction
             */
            if (!$orderGoodsDeduction->used()) {
                // 没用过 0
                return $result;
            }
            return $result->plus($orderGoodsDeduction->getUsedCoin());

        }, new InvalidVirtualCoin());
        trace_log()->deduction('订单抵扣', "完成订单商品集合计算所有已用的虚拟币");

        return $result ?: new InvalidVirtualCoin();
    }
}