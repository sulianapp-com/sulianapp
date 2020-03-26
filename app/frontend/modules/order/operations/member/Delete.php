<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */

namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;

class Delete extends OrderOperation
{
    public function getApi()
    {
        return 'order.operation.delete';
    }
    public function getName()
    {
        return '删除订单';
    }

    public function getValue()
    {
        return static::DELETE;
    }

    public function enable()
    {
        return true;
    }
}