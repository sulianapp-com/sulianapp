<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/3
 * Time: 上午11:46
 */

namespace app\frontend\models\goods;

use app\frontend\modules\orderGoods\models\PreOrderGoods;

/**
 * Class Sale
 * @package app\frontend\models\goods
 * @property string max_point_deduct
 * @property string min_point_deduct
 * @property float ed_full
 * @property float ed_reduction
 * @property string ed_areaids
 * @property int ed_num
 * @property float ed_money
 * @property int has_all_point_deduct
 * @property float all_point_deduct
 */
class Sale extends \app\common\models\Sale
{
    /**
     * 计算满额减金额
     * @param float $enough
     * @return float
     */
    public function getEnoughReductionAmount($enough)
    {
        if ($enough < $this->ed_full) {
            // 未满额
            return 0;
        }

        if ($this->ed_full < 0) {
            // 减额非正数时,记录异常
            \Log::error('商品计算满减价格时,减额数据非正数', [$this->id, $this->ed_full, $this->ed_reduction]);
            return 0;
        }
        if (!($this->ed_reduction < $enough)) {
            // 减额大于商品价格时,记录异常
            \Log::error('商品计算满减价格时,减额大于商品价格', [$this->id, $this->ed_full, $this->ed_reduction]);
        }
        return min($this->ed_reduction, $enough);
    }

    /**
     * 是否包邮
     * @param PreOrderGoods $orderGoods
     * @return bool
     */
    public function isFree(PreOrderGoods $orderGoods)
    {
        if (!isset($orderGoods->order->orderAddress)) {
            //未选择地址时
            return false;
        }

        if (!$this->inFreeArea($orderGoods)) {
            //收货地址不在包邮区域
            return false;
        }

        return $this->enoughQuantity($this->goodsTotalInOrder($orderGoods)) || $this->enoughAmount($this->goodsPriceInOrder($orderGoods));
    }

    /**
     * 获取同订单中同商品总数(包括不同规格)
     * @param PreOrderGoods $orderGoods
     * @return int
     */
    private function goodsTotalInOrder(PreOrderGoods $orderGoods)
    {
        $result =  $orderGoods->order->orderGoods->where('goods_id', $orderGoods->goods_id)->sum(function ($orderGoods) {
            return $orderGoods->total;
        });
        return $result;

    }

    /**
     * 获取同订单中同商品价格(包括不同规格)
     * @param $orderGoods
     * @return float
     */
    private function goodsPriceInOrder(PreOrderGoods $orderGoods)
    {
        $result =  $orderGoods->order->orderGoods->where('goods_id', $orderGoods->goods_id)->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getPriceCalculator()->getCurrentPrice();
        });

        return $result;
    }

    /**
     * 收货地址不在包邮区域
     * @param PreOrderGoods $orderGoods
     * @return bool
     */
    private function inFreeArea(PreOrderGoods $orderGoods)
    {
        $ed_areaids = (explode(',', $this->ed_areaids));

        if (empty($ed_areaids)) {
            return true;
        }
        if (in_array($orderGoods->order->orderAddress->city_id, $ed_areaids)) {
            return false;
        }
        return true;
    }

    /**
     * 单商品购买数量
     * @param int $total
     * @return bool
     */
    private function enoughQuantity($total)
    {
        if ($this->ed_num == false) {
            return false;
        }
        return $total >= $this->ed_num;
    }

    /**
     * 单商品价格
     * @param float $price
     * @return bool
     */
    private function enoughAmount($price)
    {
        if ($this->ed_money == false) {
            return false;
        }

        return $price >= $this->ed_money;
    }
}