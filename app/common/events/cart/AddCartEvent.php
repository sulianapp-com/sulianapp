<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/4/29
 * Time: 9:21
 */

namespace app\common\events\cart;

use app\common\events\Event;
class AddCartEvent extends Event
{
    protected $cartModel;

    public function __construct($cartModel)
    {
        $this->cartModel = $cartModel;
    }
    /**
     * (监听者)获取购物车model
     * @return mixed
     */
    public function getCartModel(){
        return $this->cartModel;
    }

}