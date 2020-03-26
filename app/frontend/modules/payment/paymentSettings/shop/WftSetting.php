<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class WftSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.wft_pay');

        return \YunShop::request()->type != 7 && !is_null($set);
    }

    public function exist()
    {
        return \Setting::get('plugin.wft_pay') !== null;
    }
}