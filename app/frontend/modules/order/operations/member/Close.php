<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */

namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;

class Close extends OrderOperation
{
    public function getApi()
    {
        return 'order.operation.close';
    }
    public function getName()
    {
        return '取消订单';
    }

    public function getValue()
    {
        return static::CANCEL;
    }

    public function enable()
    {
        return true;
    }
}