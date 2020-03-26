<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午5:51
 */

namespace app\frontend\modules\order\operations\member;


use app\frontend\modules\order\operations\OrderOperation;

class ApplyRefund extends OrderOperation
{
    public function getApi()
    {
        if ($this->no_refund) {
            return \Setting::get('shop.shop')['cservice'];
        }
        return 'refund.apply.store';
    }
    public function getValue()
    {
        return static::REFUND;
    }
    public function getName()
    {
        if ($this->no_refund) {
            return '联系客服';
        }
        return '申请退款';
    }
    public function enable()
    {
        //商品开启不可退款
        if ($this->order->no_refund) {
            return false;
        }
        return $this->order->canRefund();
    }

}