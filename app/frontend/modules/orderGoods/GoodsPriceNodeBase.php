<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 3:01 PM
 */

namespace app\frontend\modules\orderGoods;

use app\common\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\order\PriceNode;
use app\frontend\modules\orderGoods\discount\BaseDiscount;

class GoodsPriceNodeBase extends BaseOrderGoodsPriceNode
{
    function getKey()
    {
        return 'goodsPrice';
    }

    /**
     * @return mixed
     */
    function getPrice()
    {
        return $this->orderGoodsPrice->getVipPrice();
    }

}