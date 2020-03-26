<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\paymentSettings\shop;

class AlipayJsapiPaySetting extends BaseSetting
{
    public function canUse()
    {
        if(\YunShop::request()->type == 2 || \YunShop::request()->type == 7){
            return false;
        }

        if (!app('plugins')->isEnabled('face-payment')) {
            return false;
        }
        $face_setting = \Setting::get('plugin.face-payment');
        if (!$face_setting['switch'] || !$face_setting['method']['alipay'] || $face_setting['button']['alipay']) {
            return false;
        }
        // 开启微信通用支付和开启微信支付总开关,并且访问端不是app
        return \Setting::get('shop.alipay_set');
    }

    public function exist()
    {
        if(\YunShop::request()->type == 2 || \YunShop::request()->type == 7){
            return false;
        }
        if (!app('plugins')->isEnabled('face-payment')) {
            return false;
        }
        $face_setting = \Setting::get('plugin.face-payment');
        if (!$face_setting['switch'] || !$face_setting['method']['weixin'] || $face_setting['button']['alipay']) {
            return false;
        }
        return \Setting::get('shop.alipay_set') !== null;
    }
}