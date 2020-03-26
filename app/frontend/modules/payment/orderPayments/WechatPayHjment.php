<?php


namespace app\frontend\modules\payment\orderPayments;


class WechatPayHjment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('converge_pay');
    }
}