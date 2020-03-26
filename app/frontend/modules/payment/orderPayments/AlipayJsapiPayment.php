<?php


namespace app\frontend\modules\payment\orderPayments;


class AlipayJsapiPayment extends WebPayment
{
    public function canUse()
    {
        //小程序，app不支持
        if (\YunShop::request()->type == 2 || \YunShop::request()->type == 7) {
            return false;
        }
        return parent::canUse();
    }
}