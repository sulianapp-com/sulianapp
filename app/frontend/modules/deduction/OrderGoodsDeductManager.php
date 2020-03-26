<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/9
 * Time: 10:50 AM
 */

namespace app\frontend\modules\deduction;


use app\common\modules\orderGoods\models\PreOrderGoods;
use app\framework\Database\Eloquent\Collection;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;

class OrderGoodsDeductManager
{
    /**
     * @var PreOrderGoods
     */
    private $orderGoods;
    /**
     * @var OrderGoodsDeductionCollection
     */
    private $orderGoodsDeductions;

    public function __construct(PreOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
    }

    /**
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductions()
    {
        if (!isset($this->orderGoodsDeductions)) {
            $this->orderGoodsDeductions = $this->_getOrderGoodsDeductions();
        }
        return $this->orderGoodsDeductions;
    }

    /**
     * 获取并订单抵扣项并载入到订单模型中
     * @return OrderGoodsDeductionCollection
     */
    private function _getOrderGoodsDeductions()
    {
        $orderDeductions = $this->getEnableDeductions()->map(function (Deduction $deduction) {
            $preOrderGoodsDeduction = new PreOrderGoodsDeduction([
                'name' => $deduction->getName(),
                'code' => $deduction->getCode()
            ]);
            $preOrderGoodsDeduction->setOrderGoods($this->orderGoods);
            return $preOrderGoodsDeduction;
        });

        return new OrderGoodsDeductionCollection($orderDeductions->all());
    }

    /**
     * 开启的抵扣项
     * @return Collection
     */
    private function getEnableDeductions()
    {
        /**
         * 商城开启的抵扣
         * @var Collection $deductions
         */
        $deductions = Deduction::where('enable', 1)->get();
        trace_log()->deduction('订单商品开启的抵扣类型', $deductions->pluck('code')->toJson());
        if ($deductions->isEmpty()) {
            return collect();
        }
        // 过滤调无效的
        $deductions = $deductions->filter(function (Deduction $deduction) {
            /**
             * @var Deduction $deduction
             */
            return $deduction->valid();
        });

        // 按照用户勾选顺序排序
        $sort = array_flip($this->orderGoods->order->getParams('deduction_ids'));
        $deductions = $deductions->sortBy(function ($deduction) use ($sort) {
            return array_get($sort, $deduction->code, 999);
        });
        return $deductions;
    }

}