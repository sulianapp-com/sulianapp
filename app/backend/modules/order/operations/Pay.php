<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/23
 * Time: 下午1:40
 */

namespace app\backend\modules\order\operations;

class Pay extends OrderOperation
{
    public function getApi()
    {
        return 'order.pay';
    }

    public function getName()
    {
        return '确认付款';
    }

    public function getValue()
    {
        return self::ADMIN_PAY;
    }

    public function enable()
    {
        return true;
    }

    public function getType()
    {
        return self::TYPE_PRIMARY;
    }
}