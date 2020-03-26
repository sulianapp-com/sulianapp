<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 上午9:36
 */

namespace app\common\events\order;

use app\common\events\Event;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

abstract class OrderGoodsEvent extends Event
{
    private $_order_goods_model;

    public function __construct(PreOrderGoods $order_goods_model)
    {
        $this->_order_goods_model = $order_goods_model;
    }
    public function getOrderGoodsModel(){
        return $this->_order_goods_model;
    }
}