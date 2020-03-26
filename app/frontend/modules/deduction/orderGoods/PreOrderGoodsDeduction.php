<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\modules\deduction\orderGoods;

use app\common\models\VirtualCoin;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\modules\deduction\InvalidOrderDeduction;
use app\frontend\modules\deduction\orderGoods\amount\FixedAmount;
use app\frontend\modules\deduction\orderGoods\amount\GoodsPriceProportion;
use app\frontend\modules\deduction\orderGoods\amount\Invalid;
use app\frontend\modules\deduction\orderGoods\amount\OrderGoodsDeductionAmount;
use app\common\modules\orderGoods\models\PreOrderGoods;
use \app\common\models\orderGoods\OrderGoodsDeduction;

/**
 * 订单商品抵扣
 * Class PreOrderGoodsDeduction
 * @package app\frontend\models\orderGoods
 * @property string code
 * @property string name
 * @property float usable_amount
 * @property float usable_coin
 * @property float used_amount
 * @property float used_coin
 * @property int uid
 */
class PreOrderGoodsDeduction extends OrderGoodsDeduction
{
    /**
     * @var PreOrderGoods
     */
    public $orderGoods;

    /**
     * @var \app\frontend\modules\deduction\GoodsDeduction
     */
    public $goodsDeduction;

    /**
     * 可用的虚拟币
     * @var VirtualCoin
     */
    private $usablePoint;
    /**
     * 最少购买限制
     * @var VirtualCoin
     */
    private $minLimitBuyCoin;
    /**
     * 已用的虚拟币
     * @var VirtualCoin
     */
    private $usedPoint;
    /**
     * 订单抵扣模型
     * @var PreOrderDeduction
     */
    private $orderDeduction;
    /**
     * 订单商品最高抵扣金额类
     * @var OrderGoodsDeductionAmount
     */
    private $orderGoodsDeductionMaxAmount;
    /**
     * 订单商品最低抵扣金额类
     * @var OrderGoodsDeductionAmount
     */
    private $orderGoodsDeductionMinAmount;

    protected $appends = ['usable_amount', 'usable_coin'];

    public function setOrderDeduction(PreOrderDeduction $orderDeduction)
    {
        $this->orderDeduction = $orderDeduction;
    }

    public function setOrderGoods(PreOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
        $this->uid = $orderGoods->uid;
    }

    /**
     * @return float
     */
    public function getUsableAmountAttribute()
    {
        return $this->getUsableCoin()->getMoney();
    }

    /**
     * @return float|int
     */
    public function getUsableCoinAttribute()
    {
        return $this->getUsableCoin()->getCoin();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getUsedAmountAttribute()
    {
        return $this->getUsedCoin()->getMoney();
    }

    /**
     * @return float|int
     * @throws \Exception
     */
    public function getUsedCoinAttribute()
    {
        return $this->getUsedCoin()->getCoin();
    }

    /**
     * @return VirtualCoin
     */
    private function newCoin()
    {
        return app('CoinManager')->make($this->code);
    }

    /**
     * 订单抵扣模型
     * @return PreOrderDeduction
     * @throws \Exception
     */
    private function getOrderDeduction()
    {

        if (!isset($this->orderDeduction)) {
            $this->orderDeduction = $this->orderGoods->getOrder()->getOrderDeductions()->where('code', $this->code)->first();
            if (!$this->orderDeduction) {
                return new InvalidOrderDeduction();
            }
        }
        return $this->orderDeduction;
    }

    /**
     * 最多可用金额
     * @return OrderGoodsDeductionAmount
     */
    private function getOrderGoodsMaxDeductionAmount()
    {
        if (!isset($this->orderGoodsDeductionMaxAmount)) {
            // 从商品抵扣中获取到类型
            switch ($this->getGoodsDeduction()->getMaxDeductionAmountCalculationType()) {
                case 'FixedAmount':
                    $this->orderGoodsDeductionMaxAmount = new FixedAmount($this->orderGoods, $this->getGoodsDeduction());
                    trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}最大限额使用固定金额");
                    break;
                case 'GoodsPriceProportion':
                    $this->orderGoodsDeductionMaxAmount = new GoodsPriceProportion($this->orderGoods, $this->getGoodsDeduction());
                    trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}最大限额使用固定比例");
                    break;
                default:
                    $this->orderGoodsDeductionMaxAmount = new Invalid($this->orderGoods, $this->getGoodsDeduction());
                    trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}最大限额设置无效");
                    break;
            }
        }
        return $this->orderGoodsDeductionMaxAmount;
    }

    /**
     * 最小限额
     * @return OrderGoodsDeductionAmount
     */
    private function getOrderGoodsMinDeductionAmount()
    {
        if (!isset($this->orderGoodsDeductionMinAmount)) {
            // 从商品抵扣中获取到类型
            switch ($this->getGoodsDeduction()->getMinDeductionAmountCalculationType()) {
                case 'FixedAmount':
                    $this->orderGoodsDeductionMinAmount = new FixedAmount($this->orderGoods, $this->getGoodsDeduction());
                    trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}最小限额使用固定金额");
                    break;
                case 'GoodsPriceProportion':
                    $this->orderGoodsDeductionMinAmount = new GoodsPriceProportion($this->orderGoods, $this->getGoodsDeduction());
                    trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}最小限额使用固定比例");
                    break;
                default:
                    $this->orderGoodsDeductionMinAmount = new Invalid($this->orderGoods, $this->getGoodsDeduction());
                    trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}未设置最小限额类型");
                    break;
            }
        }
        return $this->orderGoodsDeductionMinAmount;
    }

    private function getGoodsDeduction()
    {
        if (!isset($this->goodsDeduction)) {
            $this->goodsDeduction = app('DeductionManager')->make('GoodsDeductionManager')->make($this->code, [$this->orderGoods->goods]);
        }
        return $this->goodsDeduction;
    }

    /**
     * 最低使用虚拟币
     * @return mixed
     */
    public function getMinLimitBuyCoin()
    {
        if (isset($this->minLimitBuyCoin)) {
            return $this->minLimitBuyCoin;
        }

        return $this->minLimitBuyCoin = $this->_getMinLimitBuyCoin();
    }

    /**
     * 最低使用虚拟币
     * @return mixed
     */
    public function _getMinLimitBuyCoin()
    {
        if (!$this->getGoodsDeduction() || !$this->getGoodsDeduction()->deductible($this->orderGoods->goods)) {
            // 购买商品不存在抵扣记录
            return $this->newCoin();
        }

        $amount = $this->getOrderGoodsMinDeductionAmount()->getMinAmount();

        $coin = $this->newCoin()->setMoney($amount);
        trace_log()->deduction("订单抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}最少需要抵扣{$coin->getMoney()}元");
        return $coin;
    }

    /**
     * 获取订单商品可用的虚拟币
     * @return VirtualCoin
     */
    public function getUsableCoin()
    {
        if (isset($this->usablePoint)) {
            return $this->usablePoint;
        }

        return $this->usablePoint = $this->_getUsableCoin();
    }

    /**
     * 获取订单商品可用的虚拟币
     * @return $this|VirtualCoin
     */
    private function _getUsableCoin()
    {
        if (!$this->getGoodsDeduction() || !$this->getGoodsDeduction()->deductible($this->orderGoods->goods)) {
            trace_log()->deduction('订单商品抵扣', "{$this->name} 购买商品不存在抵扣记录");

            // 购买商品不存在抵扣记录
            return $this->newCoin();
        }

        $amount = $this->getOrderGoodsMaxDeductionAmount()->getMaxAmount();

        $coin = $this->newCoin()->setMoney($amount);
        trace_log()->deduction("订单商品抵扣", "{$this->name} 商品{$this->orderGoods->goods_id}可抵扣{$coin->getMoney()}元");
        return $coin;
    }

    /**
     * @return $this|VirtualCoin
     * @throws \Exception
     */
    public function _getUsedCoin()
    {
        // 未选中
        if (!$this->getOrderDeduction()->isChecked()) {
            return $this->newCoin();
        }
        // 没有可用抵扣金额
        if ($this->getUsableCoin()->getMoney() <= 0) {
            return $this->newCoin();
        }
        // 订单商品最低抵扣
        $amount = $this->getMinLimitBuyCoin()->getMoney();
        // 订单所有同类型的剩余抵扣
        $restDeductionAmount = $this->getOrderDeduction()->getMaxOrderGoodsDeduction()->getMoney() - $this->getOrderDeduction()->getMinDeduction()->getMoney();
        // 订单商品的剩余抵扣/      订单所有同类型的剩余抵扣     获取订单商品占用的抵扣金额-d
        $amount += ($this->getUsableCoin()->getMoney() - $this->getMinLimitBuyCoin()->getMoney()) / $restDeductionAmount * ($this->getOrderDeduction()->getOrderGoodsDeductionAmount() - $this->getOrderDeduction()->getMinDeduction()->getMoney());

        return $this->newCoin()->setMoney($amount);
    }

    /**
     * @return VirtualCoin|PreOrderGoodsDeduction
     * @throws \Exception
     */
    public function getUsedCoin()
    {
        trace_log()->deduction('订单抵扣', "{$this->name} 订单商品计算已抵扣的虚拟币");
        if (isset($this->usedPoint)) {
            return $this->usedPoint;
        }
        return $this->usedPoint = $this->_getUsedCoin();

    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function used()
    {
        return $this->getOrderDeduction()->isChecked() && $this->getUsedCoin()->getCoin() > 0;
    }

    public function toArray()
    {
        $this->code = (string)$this->code;
        $this->name = (string)$this->name;
        $this->usable_amount = (float)$this->usable_amount;
        $this->usable_coin = (float)$this->usable_coin;
        $this->used_amount = (float)$this->used_amount;
        $this->used_coin = (float)$this->used_coin;
        return parent::toArray();
    }

    /**
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        if (!$this->used()) {
            return true;
        }
        // 确保魔术属性最少执行一次
        $this->code = (string)$this->code;
        $this->name = (string)$this->name;
        $this->usable_amount = (float)$this->usable_amount;
        $this->usable_coin = (float)$this->usable_coin;
        $this->used_amount = (float)$this->used_amount;
        $this->used_coin = (float)$this->used_coin;
        return parent::save($options);
    }
}