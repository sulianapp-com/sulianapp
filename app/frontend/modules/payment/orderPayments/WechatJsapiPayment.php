<?php


namespace app\frontend\modules\payment\orderPayments;


class WechatJsapiPayment extends BasePayment
{
    public function canUse()
    {
        if(\YunShop::request()->type == 5 || \YunShop::request()->type == 7){
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
        return parent::canUse();
    }
}