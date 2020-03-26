<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/23
 * Time: 下午1:40
 */

namespace app\backend\modules\order\operations;

class FClose extends OrderOperation
{
    public function getApi()
    {
        return 'order.close.force';
    }

    public function getName()
    {
        return '关闭订单';
    }

    public function getValue()
    {
        return self::ADMIN_CLOSE;
    }

    public function enable()
    {
        return true;
    }

    public function getType()
    {
        return self::TYPE_DANGER;
    }
}