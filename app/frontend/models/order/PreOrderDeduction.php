<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;

use app\common\exceptions\MinOrderDeductionNotEnough;
use app\common\models\order\OrderDeduction;
use app\common\models\VirtualCoin;
use app\frontend\models\MemberCoin;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\deduction\OrderGoodsDeductionCollection;
use app\frontend\modules\deduction\orderGoods\PreOrderGoodsDeduction;
use app\frontend\modules\order\models\PreOrder;

/**
 * 订单抵扣类
 * Class PreOrderDeduction
 * @package app\frontend\models\order
 * @property int uid
 * @property int coin
 * @property int amount
 * @property int name
 * @property int code
 */
class PreOrderDeduction extends OrderDeduction
{
    protected $appends = ['checked'];
    /**
     * @var PreOrder
     */
    public $order;
    /**
     * @var Deduction
     */
    private $deduction;
    /**
     * @var MemberCoin
     */
    private $memberCoin;
    /**
     * @var OrderGoodsDeductionCollection
     */
    private $orderGoodsDeductionCollection;
    /**
     * @var VirtualCoin
     */
    private $usablePoint;

    /**
     * @param Deduction $deduction
     * @param PreOrder $order
     * @param OrderGoodsDeductionCollection $orderGoodsDeductionCollection
     */
    public function init(
        Deduction $deduction,
        PreOrder $order,
        OrderGoodsDeductionCollection $orderGoodsDeductionCollection)
    {
        $this->deduction = $deduction;

        $this->setOrder($order);
        $this->setOrderGoodsDeductionCollection($orderGoodsDeductionCollection);
        $this->orderGoodsDeductionCollection->each(function (PreOrderGoodsDeduction $orderGoodsDeduction) {
            $orderGoodsDeduction->setOrderDeduction($this);
        });
    }

    public function getUidAttribute()
    {
        return $this->order->uid;
    }

    public function getCodeAttribute()
    {
        return $this->getCode();
    }

    public function getNameAttribute()
    {
        return $this->getName();
    }

    /**
     * @return float|int
     * @throws \app\common\exceptions\AppException
     */
    public function getCoinAttribute()
    {
        return $this->getMinDeduction()->getCoin() + $this->getUsablePoint()->getCoin();
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function getAmountAttribute()
    {
        return $this->getAmount();
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function getAmount()
    {

        return $this->getMinDeduction()->getMoney() + $this->getUsablePoint()->getMoney();
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Deduction
     */
    public function getDeduction()
    {
        return $this->deduction;
    }

    /**
     * @param PreOrder $order
     */
    private function setOrder(PreOrder $order)
    {
        $this->order = $order;
    }

    /**
     * 下单时此抵扣可选
     * @return bool
     */
    public function deductible()
    {
        return $this->amount > 0;
    }

    /**
     * 实例化并绑定所有的订单商品抵扣实例,集合  并将集合绑定在订单抵扣上
     * @param OrderGoodsDeductionCollection $orderGoodsDeductionCollection
     */
    private function setOrderGoodsDeductionCollection(OrderGoodsDeductionCollection $orderGoodsDeductionCollection)
    {
        $this->orderGoodsDeductionCollection = $orderGoodsDeductionCollection;
    }


    /**
     * 下单用户此抵扣对应虚拟币的余额
     * @return MemberCoin
     */
    private function getMemberCoin()
    {
        if (isset($this->memberCoin)) {
            return $this->memberCoin;
        }
        $code = $this->getCode();

        return app('CoinManager')->make('MemberCoinManager')->make($code, [$this->order->belongsToMember]);
    }

    /**
     * 此抵扣对应的虚拟币
     * @return VirtualCoin
     */
    private function newCoin()
    {
        return app('CoinManager')->make($this->getCode());
    }

    /**
     * 订单中实际可用的此抵扣
     * @return $this|VirtualCoin
     * @throws \app\common\exceptions\AppException
     */
    public function getUsablePoint()
    {

        if (!isset($this->usablePoint)) {

            trace_log()->deduction('开始订单抵扣', "{$this->getName()} 计算可用金额");

            $this->usablePoint = $this->newCoin();

            // 购买者不存在虚拟币记录
            if (!$this->getMemberCoin()) {
                trace_log()->deduction('订单抵扣', "{$this->getName()} 用户没有对应虚拟币");
                return $this->usablePoint;
            }

            // 商品金额抵扣+ 运费抵扣金额 不能超过订单当前抵扣项之前的金额
            $deductionAmount = min(
                $this->order->getPriceBefore($this->getCode() . 'RestDeduction') - $this->order->dispatch_price,
                $this->getMaxDeduction()->getMoney() - $this->getMinDeduction()->getMoney()
            );

            $deductionAmount += $this->getMaxDispatchPriceDeduction()->getMoney();

            // 用户可用虚拟币-最低抵扣 与订单抵扣虚拟币的最小值  todo 如果以后需要一种抵扣币抵扣两次时,会产生bug

            $amount = min(
                $this->getMemberCoin()->getMaxUsableCoin()->getMoney() - $this->getMinDeduction()->getMoney(),
                $deductionAmount
            );
            $this->usablePoint = $this->newCoin()->setMoney($amount);
            trace_log()->deduction("订单抵扣", "{$this->name} 可抵扣{$this->usablePoint->getMoney()}元");
        }

        return $this->usablePoint;
    }

    /**
     * 获取订单商品占用的抵扣金额
     * @return float|int
     */
    public function getOrderGoodsDeductionAmount()
    {
        $amount = ($this->getMaxOrderGoodsDeduction()->getMoney() / ($this->getMaxDeduction()->getMoney()+$this->getMaxDispatchPriceDeduction()->getMoney())) * $this->amount;
        return $amount;
    }

    /**
     * @var VirtualCoin
     */
    private $maxDeduction;

    /**
     * 订单中此抵扣可用最大值
     * @return VirtualCoin
     */
    private function getMaxDeduction()
    {
        if (!isset($this->maxDeduction)) {

            $this->maxDeduction = $this->getMaxOrderGoodsDeduction();
            trace_log()->deduction('订单抵扣', "{$this->getName()} 计算最大抵扣{$this->maxDeduction}");
        }

        return $this->maxDeduction;

    }

    /**
     * @var VirtualCoin
     */
    private $minDeduction;

    /**
     * 订单中此抵扣可用最小值
     * @return VirtualCoin
     */
    public function getMinDeduction()
    {
        if (!isset($this->minDeduction)) {
            $this->minDeduction = $this->getMinOrderGoodsDeduction();
            trace_log()->deduction('订单抵扣', "{$this->getName()} 计算最小抵扣{$this->minDeduction->getMoney()}元");

        }

        return $this->minDeduction;
    }

    /**
     * 最多可抵扣商品金额的虚拟币
     * 累加所有订单商品的可用虚拟币
     * @return VirtualCoin
     */
    public function getMaxOrderGoodsDeduction()
    {
        return $this->getOrderGoodsDeductionCollection()->getUsablePoint();
    }

    /**
     * 最低抵扣商品金额的虚拟币
     * 累加所有订单商品的可用虚拟币
     * @return VirtualCoin
     */
    public function getMinOrderGoodsDeduction()
    {
        return $this->getOrderGoodsDeductionCollection()->getMinPoint();
    }

    /**
     * 最多可抵扣运费的虚拟币
     * @return VirtualCoin
     */
    private function getMaxDispatchPriceDeduction()
    {
        $result = $this->newCoin();

        trace_log()->freight("监听抵扣", $this->getDeduction()->getName() . '运费抵扣开启状态' . $this->getDeduction()->isEnableDeductDispatchPrice());
        //开关
        if ($this->getDeduction()->isEnableDeductDispatchPrice()) {

            //订单运费
            $amount = $this->order->getDispatchAmount();

            $result->setMoney($amount);
            trace_log()->freight("监听抵扣运费", $this->getDeduction()->getName() . '运费可抵扣金额');
        }
        return $result;
    }

    /**
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductionCollection()
    {
        return $this->orderGoodsDeductionCollection;

    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getDeduction()->getCode();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getDeduction()->getName();
    }

    /**
     * @return bool
     */
    public function getCheckedAttribute()
    {
        return $this->isChecked();
    }

    private $isChecked;

    public function setChecked()
    {
        $this->isChecked = true;
    }

    /**
     * 必须选中
     * @return bool
     */
    public function mustBeChecked()
    {
        // 设置了最低抵扣必须选中
        return $this->getMinDeduction()->getMoney() > 0;
    }

    /**
     * 选择了此抵扣
     * @return bool
     */
    public function isChecked()
    {
        if (!isset($this->isChecked)) {
            if ($this->mustBeChecked()) {
                // 必须选中
                $this->isChecked = true;
            } else {
                // 用户选中
                $deduction_codes = $this->order->getParams('deduction_ids');

                if (!is_array($deduction_codes)) {
                    $deduction_codes = json_decode($deduction_codes, true);
                    if (!is_array($deduction_codes)) {
                        $deduction_codes = explode(',', $deduction_codes);
                    }
                }
                $this->isChecked = in_array($this->getCode(), $deduction_codes);
            }
        }
        return $this->isChecked;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->code = (string)$this->code;
        $this->name = (string)$this->name;
        $this->amount = sprintf('%.2f', $this->amount);
        $this->coin = sprintf('%.2f', $this->coin);
        return parent::toArray();
    }

    /**
     * @throws \app\common\exceptions\AppException
     */
    public function lock()
    {
        // 抵扣被选中后,锁定要使用的虚拟币额度
        $this->getMemberCoin()->lockCoin($this->coin);
    }

    /**
     * @return bool
     */
    public function beforeSaving()
    {
        if (!$this->isChecked() || $this->getOrderGoodsDeductionCollection()->getUsablePoint() <= 0) {
            return false;
        }
        $this->getMemberCoin()->consume($this->newCoin()->setMoney($this->amount), ['order_sn' => $this->order->order_sn]);
        $this->code = (string)$this->code;
        $this->name = (string)$this->name;
        $this->amount = sprintf('%.2f', $this->amount);
        $this->coin = sprintf('%.2f', $this->coin);
        return parent::beforeSaving();
    }

    /**
     * @throws MinOrderDeductionNotEnough
     */
    public function validateCoin()
    {
        // 验证最低抵扣大于可用抵扣
        if ($this->getMemberCoin()->getMaxUsableCoin()->getMoney() < $this->getMinDeduction()->getMoney()) {
            throw new MinOrderDeductionNotEnough("会员[{$this->getName()}]抵扣余额可抵扣金额{$this->getMemberCoin()->getMaxUsableCoin()->getMoney()}元,不满足最低抵扣金额{$this->getMinDeduction()->getMoney()}元");
        }
    }
}