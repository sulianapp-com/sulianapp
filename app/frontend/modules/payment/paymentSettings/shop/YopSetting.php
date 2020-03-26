<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/18
 * Time: 9:27
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class YopSetting extends BaseSetting
{
    private $yop_set;

    public function canUse()
    {
        $set = $this->getYopSet();

        return \YunShop::request()->type != 7 && !is_null($set) && $set['yop_wechat_pay'] != 1;
    }

    public function exist()
    {
        $set = $this->getYopSet();

        return !empty($set['merchant_no']);
    }

    private function getYopSet()
    {
        if (!app('plugins')->isEnabled('yop-pay')) {

            return null;
        }
        return \Yunshop\YopPay\models\YopSetting::getSet();
    }
}