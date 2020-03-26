<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\paymentSettings\shop;

class WechatJsapiPaySetting extends BaseSetting
{
    public function canUse()
    {
        if(\YunShop::request()->type == 5){
            return false;
        }

        if (!app('plugins')->isEnabled('face-payment')) {
            return false;
        }
        $face_setting = \Setting::get('plugin.face-payment');
        if (!$face_setting['switch'] || !$face_setting['method']['weixin'] || $face_setting['button']['wechat']) {
            return false;
        }
        // 开启微信通用支付和开启微信支付总开关,并且访问端不是app
        return \Setting::get('shop.wechat_set');
    }

    public function exist()
    {
        if(\YunShop::request()->type == 5){
            return false;
        }
        if (!app('plugins')->isEnabled('face-payment')) {
            return false;
        }
        $face_setting = \Setting::get('plugin.face-payment');
        if (!$face_setting['switch'] || !$face_setting['method']['weixin'] || $face_setting['button']['wechat']) {
            return false;
        }
        return \Setting::get('shop.wechat_set') !== null;
    }
}