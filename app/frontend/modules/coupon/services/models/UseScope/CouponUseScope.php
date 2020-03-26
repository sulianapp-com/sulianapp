<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午3:00
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\common\modules\orderGoods\OrderGoodsCollection;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;

abstract class CouponUseScope
{
    protected $orderGoods;
    public function valid()
    {
        if($this->getOrderGoodsOfUsedCoupon()->isNotEmpty()){

            //todo 此处有bug ,如果调用处提前结束了判断条件,会导致 orderGoodsGroup属性获取失败
            $this->setOrderGoodsCollection();
            return true;
        }

        return false;
    }
    /**
     * (缓存)订单中使用了该优惠券的商品组
     * @return Collection
     */
    protected function getOrderGoodsOfUsedCoupon(){
        if(isset($this->orderGoods)){
            return $this->orderGoods;
        }
        return $this->orderGoods = $this->_getOrderGoodsOfUsedCoupon();
    }
    /**
     * @var Coupon
     */
    protected $coupon;
    /**
     * @var PreOrderGoodsCollection
     */
    protected $orderGoodsGroup;
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function getOrderGoodsInScope(){
        return $this->orderGoodsGroup;
    }
    /**
     * 将订单商品装入 订单商品组对象
     */
    protected function setOrderGoodsCollection()
    {
        //dd($this->getOrderGoodsOfUsedCoupon());
        $this->orderGoodsGroup = new OrderGoodsCollection($this->getOrderGoodsOfUsedCoupon());
    }
    abstract protected function _getOrderGoodsOfUsedCoupon();
}