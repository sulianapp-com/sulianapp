<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/26
 * Time: 3:52 PM
 */

namespace app\common\modules\orderGoods;

use app\common\modules\orderGoods\models\PreOrderGoods;
use app\framework\Database\Eloquent\Collection;

class OrderGoodsCollection extends Collection
{
    public function setOrder($order){
        foreach ($this as $orderGoods){
            $orderGoods->setOrder($order);
        }
    }
    /**
     * 获取原价
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getGoodsPrice();
        });
    }

    /**
     * 获取成交价
     * @return int
     */
    public function getPrice()
    {
        return $this->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getPrice();
        });
    }

    /**
     * 获取支付价
     * @return int
     */
    public function getPaymentAmount()
    {
        return $this->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getPaymentAmount();
        });
    }

    /**
     * 获取折扣优惠券优惠金额
     * @return int
     */
    public function getCouponDiscountPrice()
    {
        return $this->sum(function ($orderGoods) {
            return $orderGoods->couponDiscountPrice;
        });
    }

    /**
     * 订单商品集合中包含虚拟物品
     * @return bool
     */
    public function hasVirtual(){
        $bool = $this->contains(function ($aOrderGoods) {
            // 包含虚拟商品
            //return $aOrderGoods->goods->type == 2;
            //todo 20190107 blank 修改 ：包含实体商品按实体商品下单流程走
            return $aOrderGoods->goods->type == 1;
        });

        return !$bool;
    }

    /**
     * 订单商品集合中包含不需要地址的物品
     * @return bool
     */
    public function hasNeedAddress()
    {
        $bool = $this->contains(function ($aOrderGoods) {
            // 包含不需要地址的商品
            //return $aOrderGoods->goods->need_address == 1;
            //todo 20190107 blank 修改 ：包含需要地址的商品按标准下单流程走
            return $aOrderGoods->goods->need_address != 1;
        });

        return !$bool;
    }
}