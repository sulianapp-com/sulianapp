<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\order\discount;

use app\common\modules\orderGoods\models\PreOrderGoods;

/**
 * 单品满减优惠
 * Class SingleEnoughReduce
 * @package app\frontend\modules\order\discount
 */
class SingleEnoughReduce extends BaseDiscount
{
    protected $code = 'singleEnoughReduce';
    protected $name = '单品满减优惠';

    /**
     * 订单中订单商品单品满减的总金额
     * @return float
     */
    protected function _getAmount()
    {
        //对订单商品按goods_id去重 累加单品满减金额
        $result = $this->order->orderGoods->unique('goods_id')->sum(function (PreOrderGoods $orderGoods) {
            return $this->totalAmount($orderGoods);

        });

        return $result;
    }

    /**
     * 指定订单商品的单品满减金额
     * @param PreOrderGoods $orderGoods
     * @return float
     */
    private function totalAmount(PreOrderGoods $orderGoods)
    {
        // 求和所属订单中指定goods_id的订单商品支付金额
        $amount = $this->order->orderGoods->where('goods_id', $orderGoods->goods_id)->sum(function (PreOrderGoods $preOrderGoods) {
            return $preOrderGoods->getPriceBefore($this->getCode());
        });
        if (is_null($orderGoods->goods->hasOneSale)) {
            return 0;
        }
        // order_goods的单品满减金额
        return $orderGoods->goods->hasOneSale->getEnoughReductionAmount($amount);
    }
}