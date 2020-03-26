<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */
namespace app\frontend\modules\payment\paymentSettings\shop;

class AlipaySetting extends BaseSetting
{
    public function canUse()
    {
        return \YunShop::request()->type != 2 && \Setting::get('shop.pay.alipay');
    }
    public function exist()
    {
        return \Setting::get('shop.pay.alipay') !== null;
    }
}