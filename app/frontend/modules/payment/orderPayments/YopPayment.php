<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/18
 * Time: 9:16
 */

namespace app\frontend\modules\payment\orderPayments;


class YopPayment extends WebPayment
{
    public function canUse()
    {
        //&& \YunShop::request()->type == 1;
        return parent::canUse() && \YunShop::plugin()->get('yop-pay');
    }
}