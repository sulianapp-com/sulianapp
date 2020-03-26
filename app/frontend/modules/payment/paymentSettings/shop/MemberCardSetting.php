<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/10
 * Time: 下午1:09
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class MemberCardSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.pet');

        return \YunShop::request()->type != 53 && !is_null($set) && 1 == $set['is_open_pet'];
    }

    public function exist()
    {
        return \Setting::get('plugin.pet') !== null;
    }
}