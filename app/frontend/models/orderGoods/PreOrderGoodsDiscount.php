<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\frontend\models\orderGoods;

use app\common\models\orderGoods\OrderGoodsDiscount;
use app\common\modules\orderGoods\models\PreOrderGoods;

class PreOrderGoodsDiscount extends OrderGoodsDiscount
{
    public $orderGoods;

    public function setOrderGoods(PreOrderGoods $orderGoods)
    {
        $this->orderGoods = $orderGoods;
        $this->uid = $this->orderGoods->uid;

        $orderGoods->getOrderGoodsDiscounts()->push($this);

    }

}