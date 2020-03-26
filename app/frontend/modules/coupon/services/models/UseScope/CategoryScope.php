<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午2:44
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\common\exceptions\AppException;
use app\common\models\GoodsCategory;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

class CategoryScope extends CouponUseScope
{
    /**
    
     * @throws AppException
     */
    protected function _getOrderGoodsOfUsedCoupon()
    {
        $orderGoods = $this->coupon->getPreOrder()->orderGoods->filter(
            function ($orderGoods) {
                // todo 排除掉供应商商品 ,临时解决
                if($orderGoods->belongsToGood->is_plugin){
                    return false;
                }
                /**
                 * @var $orderGoods PreOrderGoods
                 */
                //订单商品所属的所有分类id                            //关联商品所属分类id
                $orderGoodsCategoryIds = $orderGoods->belongsToGood->belongsToCategorys->reduce(function($result,GoodsCategory $goodsCategory)
                {
                    //合并数组
                    return array_merge($result,explode(',',$goodsCategory->category_ids));
                },[]);
//                dd($orderGoodsCategoryIds);
                $orderGoodsCategoryIds = array_unique($orderGoodsCategoryIds);

                trace_log()->coupon("优惠券{$this->coupon->getMemberCoupon()->id}","商品品类".json_encode($orderGoodsCategoryIds).",优惠券支持品类".json_encode($this->coupon->getMemberCoupon()->belongsToCoupon->category_ids));
                //优惠券的分类id数组 与 订单商品的所属分类 的分类数组 有交集
                return collect($this->coupon->getMemberCoupon()->belongsToCoupon->category_ids)
                    ->intersect($orderGoodsCategoryIds)->isNotEmpty();
            });

        if ($orderGoods->unique('is_plugin')->count() > 1) {
            throw new AppException('自营商品与第三方商品不能共用一张优惠券');
        }

        return $orderGoods;
    }

}