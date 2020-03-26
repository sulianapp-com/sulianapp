<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/3
 * Time: 下午6:13
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class HuanxunWxPaySetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.huanxun_set');

        return \YunShop::request()->type != 7 && !is_null($set) && 1 == $set['switch'];
    }

    public function exist()
    {
        return \Setting::get('plugin.huanxun_set') !== null;
    }
}