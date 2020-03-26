<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/22
 * Time: 10:21
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class LcgBankCardSetting extends BaseSetting
{
    public function canUse()
    {

        return \YunShop::request()->type != 7 && \Setting::get('plugin.dragon_deposit.lcgBankCard') != '1';
    }

    public function exist()
    {
        if (!app('plugins')->isEnabled('dragon-deposit')) { return false; }

        $set = \Setting::get('plugin.dragon_deposit');

        return !empty($set['PID']);
    }
}