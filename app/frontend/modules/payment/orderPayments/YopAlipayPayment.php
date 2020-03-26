<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/4
 * Time: 10:34
 */

namespace app\frontend\modules\payment\orderPayments;


class YopAlipayPayment extends WebPayment
{
    public function canUse()
    {
        //&& \YunShop::request()->type == 1;
        return parent::canUse() && \YunShop::plugin()->get('yop-pay');
    }
}