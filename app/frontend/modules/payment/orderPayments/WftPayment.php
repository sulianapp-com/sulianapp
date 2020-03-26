<?php


namespace app\frontend\modules\payment\orderPayments;


class WftPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('wft-pay');
    }
}