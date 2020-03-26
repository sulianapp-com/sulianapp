<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 下午3:55
 */

namespace app\frontend\modules\orderGoods\discount;

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
     * 获取金额
     * @return float|int|null
     */
    protected function _getAmount()
    {
        // (订单商品成交金额/订单中同种商品总成交金额 ) * 订单单品满减金额
        // 商品成交金额 = 订单成交价 - 商品等级优惠
        return ($this->orderGoods->getPriceBefore($this->getCode()) / $this->getOrderGoodsPrice()) * $this->getAmountInOrder();
    }

    /**
     * 订单对应该商品的单品优惠
     */
    private function getAmountInOrder()
    {
        if(is_null($this->orderGoods->goods->hasOneSale)){
            return 0;
        }
        return $this->orderGoods->goods->hasOneSale->getEnoughReductionAmount($this->getOrderGoodsPrice());
    }

    /**
     * 订单中同商品的价格小计
     * @return float
     */
    protected function getOrderGoodsPrice()
    {
        return $this->orderGoods->order->orderGoods->where('goods_id', $this->orderGoods->goods_id)->sum(function (PreOrderGoods $preOrderGoods) {
            return $preOrderGoods->getPriceBefore($this->getCode());
        });
    }

}