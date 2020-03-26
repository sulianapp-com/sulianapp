<?php

namespace app\frontend\modules\coupon\services\models\UseScope;

use app\common\exceptions\AppException;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午1:53
 */
class GoodsScope extends CouponUseScope
{

    /**
     * 订单中使用了该优惠券的商品组
     * @return Collection
     * @throws AppException
     */
    protected function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods = $this->coupon->getPreOrder()->orderGoods->filter(
            function ($orderGoods) {
                /**
                 * @var $orderGoods PreOrderGoods
                 */
                trace_log()->coupon("优惠券{$this->coupon->getMemberCoupon()->id}","商品id{$orderGoods->goods_id},优惠券支持品商品id{$this->coupon->getMemberCoupon()->belongsToCoupon->goods_ids}");
                return in_array($orderGoods->goods_id, $this->coupon->getMemberCoupon()->belongsToCoupon->goods_ids);
            });
        if ($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }

}