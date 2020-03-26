<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/9
 * Time: 10:50 AM
 */

namespace app\frontend\modules\deduction;


use app\common\exceptions\AppException;
use app\common\modules\orderGoods\models\PreOrderGoods;
use app\framework\Database\Eloquent\Collection;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\order\models\PreOrder;

class OrderDeductManager
{
    /**
     * @var PreOrder
     */
    private $order;

    /**
     * @var OrderDeductionCollection
     */
    private $orderDeductionCollection;
    /**
     * @var OrderDeductionCollection
     */
    private $checkedOrderDeductionCollection;
    /**
     * @var OrderGoodsDeductionCollection
     */
    private $orderGoodsDeductionCollection;
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $deductions;

    public function __construct(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * @return OrderDeductionCollection
     * @throws AppException
     */
    public function getOrderDeductions()
    {
        if (!isset($this->orderDeductionCollection)) {
            $this->orderDeductionCollection = $this->getAllOrderDeductions();
            $this->order->setRelation('orderDeductions',$this->orderDeductionCollection);
            // 按照选中状态排序
            $this->orderDeductionCollection->sortOrderDeductionCollection();
            // 验证
            $this->orderDeductionCollection->validate();


        }
        return $this->orderDeductionCollection;
    }

    /**
     * 获取并订单抵扣项并载入到订单模型中
     * @return OrderDeductionCollection
     */
    public function getAllOrderDeductions()
    {
        $orderDeductions = $this->getEnableDeductions()->map(function (Deduction $deduction) {

            $orderGoodsDeductionCollection = $this->getOrderGoodsDeductionCollection()->where('code', $deduction->getCode());

            /**
             * @var PreOrderDeduction $orderDeduction
             */
            $orderDeduction = new PreOrderDeduction();

            $orderDeduction->init($deduction, $this->order, $orderGoodsDeductionCollection);
            return $orderDeduction;
        });

        return new OrderDeductionCollection($orderDeductions->all());
    }

    /**
     * @param $deductions
     */
    public function setDeductions(\Illuminate\Database\Eloquent\Collection $deductions)
    {
        $this->deductions = $deductions;
    }

    /**
     * @param OrderGoodsDeductionCollection $orderGoodsDeductionCollection
     */
    public function setOrderGoodsDeductionCollection(OrderGoodsDeductionCollection $orderGoodsDeductionCollection)
    {
        $this->orderGoodsDeductionCollection = $orderGoodsDeductionCollection;
    }

    /**
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductionCollection()
    {
        if (!isset($this->orderGoodsDeductionCollection)) {
            $orderGoodsDeductions = $this->order->orderGoods->flatMap(function (PreOrderGoods $orderGoods) {
                return $orderGoods->getOrderGoodsDeductions();
            });
            $this->orderGoodsDeductionCollection = new OrderGoodsDeductionCollection($orderGoodsDeductions->all());
        }
        return $this->orderGoodsDeductionCollection;
    }

    /**
     * 开启的抵扣项
     * @return Collection
     */
    private function getEnableDeductions()
    {
        if (!isset($this->deductions)) {
            /**
             * 商城开启的抵扣
             * @var Collection $deductions
             */
            $deductions = Deduction::where('enable', 1)->get();
            trace_log()->deduction('开启的抵扣类型', $deductions->pluck('code')->toJson());
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
            $sort = array_flip($this->order->getParams('deduction_ids'));
            $this->deductions = $deductions->sortBy(function ($deduction) use ($sort) {
                return array_get($sort, $deduction->code, 999);
            });
        }

        return $this->deductions;
    }

    /**
     * @return OrderDeductionCollection|static
     * @throws AppException
     */
    public function getCheckedOrderDeductions()
    {
        if (!isset($this->checkedOrderDeductionCollection)) {
            // 求和订单抵扣集合中所有已选中的可用金额
            $this->checkedOrderDeductionCollection = $this->getOrderDeductions()->filter(function (PreOrderDeduction $orderDeduction) {
                return $orderDeduction->isChecked();
            });
            // 过滤调不能抵扣的项
            $this->checkedOrderDeductionCollection->filterNotDeductible();
            $this->checkedOrderDeductionCollection->lock();
            if ($this->checkedOrderDeductionCollection->minAmount() > $this->order->getPriceBefore('orderDispatchPrice')) {
                throw new AppException("订单支付总金额{$this->order->getPriceBefore('orderDispatchPrice')}元,不满足最低抵扣总金额{$this->checkedOrderDeductionCollection->minAmount()}元");
            }


        }

        // 返回 订单抵扣金额
        return $this->checkedOrderDeductionCollection;
    }
}