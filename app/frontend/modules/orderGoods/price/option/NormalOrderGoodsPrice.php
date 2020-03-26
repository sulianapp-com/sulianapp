<?php

namespace app\frontend\modules\orderGoods\price\option;


/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/19
 * Time: 下午6:04
 */
class NormalOrderGoodsPrice extends BaseOrderGoodsPrice
{

    /**
     * 商品的原价,为了规格继承时将属性名替换掉
     * @return mixed
     */
    protected function aGoodsPrice()
    {
        return $this->goods()->dealPrice;
    }
    /**
     * 获取商品的模型,规格继承时复写这个方法
     * @return mixed
     */
    protected function goods()
    {
        return $this->orderGoods->goods;
    }



}