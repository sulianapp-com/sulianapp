<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class UsdtPaySetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.usdtpay_set.usdt_status');
        return \YunShop::request()->type != 7 && $set;
    }

    public function exist()
    {

        return \Setting::get('plugin.usdtpay_set') !== null;
    }
}