<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/5
 * Time: 上午11:33
 */

namespace app\common\events\cart;

use app\common\events\Event;

class GroupingCartEvent extends Event
{
    protected $carts;

    public function __construct()
    {
        $params = \YunShop::request()->get();

        $this->carts = $params['goods'];
    }
    /**
     * (监听者)获取购物车model
     * @return mixed
     */
    public function getCarts(){
        return $this->carts;
    }
}