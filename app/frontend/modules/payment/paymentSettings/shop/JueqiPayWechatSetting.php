<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


use app\common\helpers\Client;

class JueqiPayWechatSetting extends BaseSetting
{
    public function canUse()
    {

        $set = \Setting::get('plugin.jueqi_pay_set');
        return \YunShop::request()->type != 7 && !is_null($set) && 1 == $set['switch'];
    }

    public function exist()
    {

        if(Client::is_weixin() !== true){
            return false;
        }
        return  \Setting::get('shop.pay.weixin') !== null && \Setting::get('plugin.jueqi_pay_set') !== null;
    }
}