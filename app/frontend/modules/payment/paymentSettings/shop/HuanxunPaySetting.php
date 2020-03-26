<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/6/28
 * Time: 下午5:01
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class HuanxunPaySetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.huanxun_set');

        return \YunShop::request()->type != 7 && !is_null($set) && 1 == $set['quick_switch'];
    }

    public function exist()
    {
        return \Setting::get('plugin.huanxun_set') !== null;
    }
}