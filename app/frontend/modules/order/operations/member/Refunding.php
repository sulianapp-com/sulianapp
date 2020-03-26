<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午5:51
 */

namespace app\frontend\modules\order\operations\member;


use app\frontend\modules\order\operations\OrderOperation;

class Refunding extends OrderOperation
{
    public function getApi()
    {
        return 'refund.detail';
    }
    public function getValue()
    {
        return static::REFUND_INFO;
    }
    public function getName()
    {
        return '退款中';
    }
    public function enable()
    {
        //2018-8-30 租赁订单不能退款
        if ($this->order->plugin_id == 40) {
            return false;
        }

        return $this->order->isRefunding();

    }
}