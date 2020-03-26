<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 3:01 PM
 */

namespace app\frontend\modules\orderGoods;

use app\common\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\order\PriceNode;
use app\frontend\modules\orderGoods\discount\BaseDiscount;
use app\frontend\modules\orderGoods\price\option\BaseOrderGoodsPrice;

class OrderGoodsCouponPriceNode extends BaseOrderGoodsPriceNode
{
    function getKey()
    {
        return 'coupon';
    }

    /**
     * @return float|int|mixed
     * @throws \app\common\exceptions\AppException
     */
    function getPrice()
    {
        // todo 订单优惠券计算需要参照抵扣的结构重构, 这里先调用一次订单的抵扣金额,来保证先绑定订单商品优惠券的模型,后通过模型获取订单商品优惠券总金额
        $this->orderGoodsPrice->orderGoods->order->getPriceAfter('coupon');

        return max($this->orderGoodsPrice->getPriceBefore($this->getKey()) - $this->orderGoodsPrice->getCouponAmount(),0);
    }

}