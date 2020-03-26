<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\orderGoods\discount;

use app\common\modules\orderGoods\models\PreOrderGoods;

class EnoughReduce extends BaseDiscount
{
    protected $code = 'enoughReduce';
    protected $name = '全场满减优惠';

    /**
     * @return float|int|null
     */
    protected function _getAmount()
    {
        // (支付金额/订单中同种商品已计算的支付总价 ) * 全场满减金额
        return ($this->orderGoods->getPriceBefore($this->getCode()) / $this->getOrderGoodsPrice()) * $this->getAmountInOrder();
    }

    /**
     * 订单此种优惠总金额
     * @return float
     */
    protected function getAmountInOrder()
    {
        return $this->orderGoods->order->getDiscount()->getAmountByCode($this->code)->getAmount();
    }

    /**
     * 订单中同商品的价格小计
     * @return float
     */
    protected function getOrderGoodsPrice()
    {
        return $this->orderGoods->order->orderGoods->sum(function (PreOrderGoods $preOrderGoods) {
            return $preOrderGoods->getPriceBefore($this->getCode());
        });
    }
}