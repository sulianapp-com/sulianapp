<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/15
 * Time: 下午3:42
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class AnotherPaySetting extends BaseSetting
{
    public function canUse()
    {
        return \Setting::get('shop.pay.another');
    }

    public function exist()
    {
        return \Setting::get('shop.pay.another') !== null;
    }
}