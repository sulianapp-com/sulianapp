<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\paymentSettings\shop;

class WechatPaySetting extends BaseSetting
{
    public function canUse()
    {
        if(\YunShop::request()->type == 5){
            return false;
        }
        // 开启微信通用支付和开启微信支付总开关,并且访问端不是app
        return \Setting::get('shop.pay.weixin') && \Setting::get('shop.pay.weixin_pay');
    }

    public function exist()
    {
        if(\YunShop::request()->type == 5){
            return false;
        }
        return \Setting::get('shop.pay.weixin') !== null || \Setting::get('shop.pay.weixin_pay') !== null;
    }
}