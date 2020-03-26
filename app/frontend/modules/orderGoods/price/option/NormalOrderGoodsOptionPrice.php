<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/25
 * Time: 下午3:32
 */

namespace app\frontend\modules\orderGoods\price\option;

class NormalOrderGoodsOptionPrice extends NormalOrderGoodsPrice
{
    protected function goods(){
        return $this->orderGoods->goodsOption;
    }
    protected function aGoodsPrice(){
        return $this->goods()->deal_price;
    }
}