<?php


namespace app\frontend\modules\payment\orderPayments;


class DianBangScanPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('dian-bang-scan');
    }
}