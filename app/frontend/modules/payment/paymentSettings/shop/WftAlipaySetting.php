<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18
 * Time: 16:46
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class WftAlipaySetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.wft_alipay');

        return \YunShop::request()->type != 7 && !is_null($set);
    }

    public function exist()
    {
        return \Setting::get('plugin.wft_alipay') !== null;
    }
}