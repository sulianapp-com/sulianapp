<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\paymentSettings\shop;

class BalanceSetting extends BaseSetting
{
    public function canUse()
    {
        return \Setting::get('shop.pay.credit');
    }

    public function exist()
    {
        return \Setting::get('shop.pay.credit') !== null;
    }
}