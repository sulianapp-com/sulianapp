<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/2
 * Time: 下午1:40
 */

namespace app\frontend\modules\payment\orderPayments;


class CloudPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('cloud-pay');
    }
}