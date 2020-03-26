<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/17
 * Time: 19:58
 */

namespace app\frontend\modules\order\operations\member;


use app\frontend\modules\order\operations\OrderOperation;

class ContactCustomerService extends OrderOperation
{

    public function getApi()
    {
        return \Setting::get('shop.shop')['cservice'];
    }
    public function getValue()
    {
        return static::CONTACT_CUSTOMER_SERVICE;
    }
    public function getName()
    {
        return '联系客服';
    }
    public function enable()
    {
        //商品开启不可退款
        if (!$this->order->no_refund) {
            return false;
        }

        return true;
    }
}