<?php


namespace app\frontend\modules\payment\orderPayments;


class UsdtPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('usdtpay');
    }
}