<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class AlipayFacePaySetting extends BaseSetting
{
    public function canUse()
    {
//        $set = \Setting::get('plugin.convergePay_set.wechat.wechat_status');
        return \YunShop::request()->type == 9;
    }

    public function exist()
    {
        return true;
    }
}