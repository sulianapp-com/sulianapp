<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/16
 * Time: 上午9:44
 */

namespace app\frontend\modules\payment\orderPayments;


class AliPayment extends WebPayment
{
    public function canUse()
    {
        // 小程序不支持支付宝网页支付
        if(\YunShop::request()->type == 2){
            return false;
        }
        return parent::canUse();
    }
}