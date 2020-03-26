<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class WechatToutiaoSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.toutiao-mini.wx_switch');
        //头条登录才可使用
        return \YunShop::request()->type == 11 && $set;
    }

    public function exist()
    {
        return \Setting::get('plugin.toutiao-mini') !== null;
    }
}