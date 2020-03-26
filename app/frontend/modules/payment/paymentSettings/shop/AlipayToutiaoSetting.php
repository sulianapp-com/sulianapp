<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class AlipayToutiaoSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.toutiao-mini.alipay_switch');
        //头条才可使用
        return \YunShop::request()->type == 11 && $set;
    }

    public function exist()
    {
        return \Setting::get('plugin.toutiao-mini') !== null;
    }
}