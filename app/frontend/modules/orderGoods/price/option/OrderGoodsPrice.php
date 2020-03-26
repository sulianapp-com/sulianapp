<?php

namespace app\frontend\modules\orderGoods\price\option;

use app\frontend\modules\orderGoods\discount\EnoughReduce;
use app\frontend\modules\orderGoods\discount\SingleEnoughReduce;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/19
 * Time: 下午6:05
 */
abstract class OrderGoodsPrice
{
    /**
     * 需要重新计算
     * @var bool
     */
    protected $needCalculation = true;

    protected $goodsPrice;
    /**
     * @var \app\frontend\modules\orderGoods\models\PreOrderGoods
     */
    public $orderGoods;
    /**
     * @var SingleEnoughReduce
     */
    protected $singleEnoughReduce;
    /**
     * @var EnoughReduce
     */
    protected $enoughReduce;
    public function __construct($preOrderGoods)
    {
        $this->orderGoods = $preOrderGoods;
        $this->singleEnoughReduce = new SingleEnoughReduce($this->orderGoods);
        $this->enoughReduce = new EnoughReduce($this->orderGoods);
    }

    /**
     * 计算成交价格
     * @return float
     */
    abstract public function getPrice();

    /**
     * 计算商品销售价格
     * @return float
     */
    abstract public function getGoodsPrice();

    /**
     * 计算商品市场价格
     * @return float
     */
    abstract public function getGoodsMarketPrice();

    /**
     * 计算商品市场价格
     * @return float
     */
    abstract public function getGoodsCostPrice();

}