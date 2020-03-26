<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/1
 * Time: 下午10:55
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class CloudPayWechatSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.cloud_pay_set');

        return \YunShop::request()->type != 7 && !is_null($set) && 1 == $set['switch'] && \Setting::get('shop.pay.weixin');
    }
    public function exist()
    {
        return  \Setting::get('shop.pay.weixin') !== null && \Setting::get('plugin.cloud_pay_set') !== null;
    }
}