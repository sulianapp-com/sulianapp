<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4
 * Time: 10:37
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class YopAlipaySetting extends BaseSetting
{

    public function canUse()
    {
        $set = $this->getYopSet();


        return \YunShop::request()->type != 2 && \YunShop::request()->type != 7 && !is_null($set) && $set['yop_alipay_pay'] != 1;
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