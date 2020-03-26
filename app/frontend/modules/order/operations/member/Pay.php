<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */

namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;

class Pay extends OrderOperation
{
    public function getApi()
    {
        return 'order.operation.pay';
    }

    public function getName()
    {
        return '支付';
    }

    public function getValue()
    {
        return static::PAY;
    }

    public function enable()
    {
        if ($this->order->isPending()) {
            return false;
        }
        return true;
    }
}