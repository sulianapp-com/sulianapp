<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/5
 * Time: 上午11:33
 */

namespace app\common\events\cart;

use app\common\events\Event;

class DeleteCartEvent extends Event
{
    protected $cartsId;

    public function __construct($cartsId)
    {
        $this->cartsId = $cartsId;
    }
    /**
     * (监听者)获取购物车model
     * @return mixed
     */
    public function getCartsId(){
        return $this->cartsId;
    }
}