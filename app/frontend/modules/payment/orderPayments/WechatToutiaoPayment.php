<?php


namespace app\frontend\modules\payment\orderPayments;


class WechatToutiaoPayment extends BasePayment
{
    public function canUse()
    {
        // 只支持头条小程序
        if(\YunShop::request()->type != 11){
            return false;
        }
        return parent::canUse();
    }
}