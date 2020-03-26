<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/2
 * Time: 下午1:40
 */

namespace app\frontend\modules\payment\orderPayments;

class RemittancePayment extends BasePayment
{

    public function canUse()
    {
        return parent::canUse() && !$this->unCreateOrder();
    }

    private function unCreateOrder()
    {
        foreach ($this->orderPay->orders as $order){
            if(!isset($order->id)){
                return true;
            }
        }
        return false;
    }
}