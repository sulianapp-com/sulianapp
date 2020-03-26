<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */

namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;

class CheckInvoice extends OrderOperation
{
    public function getApi()
    {
        return 'order.operation.check-invoice';
    }
    public function getName()
    {
        return '查看发票';
    }
    public function getValue()
    {
        return static::CHECK_INVOICE;
    }

    public function enable()
    {
        //if ('0'==$this->order->call) {
        $trade = \Setting::get('shop.trade')['invoice'];
        if (!isset($trade) || '0'==$this->order->call ){
            return false;
        }
        return true;
    }
}