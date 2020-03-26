<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/2
 * Time: 下午1:40
 */

namespace app\frontend\modules\payment\orderPayments;

class CODPayment extends BasePayment
{

    public function canUse()
    {
        return parent::canUse() && !$this->hasVirtual();
    }

    private function hasVirtual()
    {
        foreach ($this->orderPay->orders as $order){
            if($order->isVirtual()){
                return true;
            }
        }
        return false;
    }
}