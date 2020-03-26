<?php
/**
 * 未生成的订单商品类
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\common\modules\orderGoods\models;

use app\common\exceptions\AppException;
use app\common\models\Goods;
use app\frontend\modules\deduction\OrderGoodsDeductionCollection;
use app\frontend\modules\orderGoods\price\option\NormalOrderGoodsPrice;

trait PreOrderGoodsTrait
{
    protected $priceCalculator;

    /**
     * todo 去除对执行顺序的依赖
     * 获取生成前的模型属性
     * @return array
     * @deprecated
     */
    public function getPreAttributes()
    {
        $attributes = array(
            'goods_id' => $this->goods->id,
            'goods_sn' => $this->goods->goods_sn,
            'title' => $this->goods->title,
            'thumb' => yz_tomedia($this->goods->thumb),
            'goods_price' => $this->getGoodsPrice(),
            'price' => $this->getPrice(),
            'goods_cost_price' => $this->getGoodsCostPrice(),
            'goods_market_price' => $this->getGoodsMarketPrice(),
            'need_address' => $this->goods->need_address,
        );

        if ($this->isOption()) {

            $attributes += [
                'goods_option_id' => $this->goodsOption->id,
                'goods_option_title' => $this->goodsOption->title,
            ];
        }

        $attributes = array_merge($this->getAttributes(), $attributes);

        return $attributes;
    }

    /**
     * @return Goods
     * @throws AppException
     */
    public function getGoods()
    {
        if (!isset($this->goods)) {
            throw new AppException('调用顺序错误,Goods对象还没有载入');
        }
        return $this->goods;
    }

    /**
     * @return int
     * @throws AppException
     */
    public function getThumbAttribute()
    {
        return yz_tomedia($this->getGoods()->thumb);
    }

    public function getNeedAddressAttribute()
    {
        return $this->goods->need_address;
    }

    /**
     * @return int
     * @throws AppException
     */
    public function getTotalAttribute()
    {
        if (!isset($this->attributes['total'])) {
            throw new AppException("orderGoods:{$this->orderGoods->goods_id}的total为null");
        }
        if ($this->attributes['total'] <= 0) {
            throw new AppException("orderGoods:{$this->orderGoods->goods_id}的total为{$this->attributes['total']}");
        }
        return $this->attributes['total'];
    }

    /**
     * @return string
     * @throws AppException
     */
    public function getGoodsSnAttribute()
    {
        return $this->getGoods()->goods_sn;
    }

    /**
     * @return string
     * @throws AppException
     */
    public function getProductSnAttribute()
    {
        return $this->getGoods()->product_sn;
    }

    /**
     * @return string
     * @throws AppException
     */
    public function getTitleAttribute()
    {
        return $this->getGoods()->title;
    }

    /**
     * @return float
     */
    public function getPriceAttribute()
    {
        return $this->getPrice();
    }

    /**
     * @return float
     */
    public function getGoodsCostPriceAttribute()
    {
        return $this->getGoodsCostPrice();
    }

    /**
     * @return float
     */
    public function getGoodsMarketPriceAttribute()
    {
        return $this->getGoodsMarketPrice();
    }

    public function getGoodsOptionTitleAttribute()
    {
        return $this->isOption() ? $this->goodsOption->title : '';
    }


    /**
     * 获取利润
     * @return mixed
     */
    public function getGoodsCostPrice()
    {
        return $this->getPriceCalculator()->getGoodsCostPrice();

    }

    /**
     * 市场价
     * @return mixed
     */
    public function getGoodsMarketPrice()
    {
        return $this->getPriceCalculator()->getGoodsMarketPrice();

    }

    /**
     * 订单商品抵扣集合
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductions()
    {
        return $this->orderGoodsDeductions;
    }

    /**
     * 获取重量
     * @return mixed
     */
    public function getWeight()
    {
        if ($this->isOption()) {
            return $this->goodsOption->weight;
        }
        return $this->goods->weight;
    }

    /**
     * 成交价格
     * @return mixed
     */
    public function getPrice()
    {
        return $this->getPriceCalculator()->getPrice();
    }

    /**
     * 原始价格
     * @return mixed
     */
    public function getGoodsPrice()
    {
        return $this->getPriceCalculator()->getGoodsPrice();
    }

    /**
     * 获取价格计算者
     * @return NormalOrderGoodsPrice
     */
    public function getPriceCalculator()
    {
        if (!isset($this->priceCalculator)) {
            $this->priceCalculator = $this->_getPriceCalculator();
        }
        return $this->priceCalculator;
    }
}