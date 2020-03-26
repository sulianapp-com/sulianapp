<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/16
 * Time: 上午9:44
 */

namespace app\frontend\modules\payment\orderPayments;


class CloudAliPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('cloud-pay');
    }
}