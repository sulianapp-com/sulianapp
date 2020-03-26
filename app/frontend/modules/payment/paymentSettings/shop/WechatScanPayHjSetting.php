<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class WechatScanPayHjSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.convergePay_set.wechat.wechat_status');
        return \YunShop::request()->type != 9 && $set;
    }

    public function exist()
    {
        return \Setting::get('plugin.convergePay_set') !== null;
    }
}