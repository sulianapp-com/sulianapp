<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/10
 * Time: 下午1:09
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class YunPayWechatSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.yun_pay_set');

        return \YunShop::request()->type != 7 && !is_null($set) && 1 == $set['switch'];
    }

    public function exist()
    {
        return \Setting::get('plugin.yun_pay_set') !== null;
    }
}