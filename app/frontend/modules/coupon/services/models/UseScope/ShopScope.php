<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午2:44
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\common\exceptions\AppException;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

class ShopScope extends CouponUseScope
{
    /**
     * @return static
     * @throws AppException
     */
    protected function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods = $this->coupon->getPreOrder()->orderGoods->filter(
            function ($orderGoods) {
                /**
                 * @var $orderGoods PreOrderGoods
                 */
                return !$orderGoods->goods->is_plugin;
            });

        if ($orderGoods->unique('is_plugin')->count() > 1) {
            trace_log()->coupon("优惠券{$this->coupon->getMemberCoupon()->id}","优惠券范围为自营,商品id{$orderGoods->goods_id}是供应商商品");
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }
}