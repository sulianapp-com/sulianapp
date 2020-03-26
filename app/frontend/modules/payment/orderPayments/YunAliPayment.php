<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/6
 * Time: 下午4:21
 */

namespace app\frontend\modules\payment\orderPayments;


class YunAliPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('yun-pay');
    }
}