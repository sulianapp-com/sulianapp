<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/10
 * Time: 下午1:07
 */

namespace app\frontend\modules\payment\orderPayments;


class MemberCardPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('pet');
    }
}
