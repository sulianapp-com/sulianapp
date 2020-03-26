<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/22
 * Time: 10:14
 */

namespace app\frontend\modules\payment\orderPayments;


class LcgBalancePayment extends WebPayment
{
    public function canUse()
    {
        //&& \YunShop::request()->type == 1;
        return parent::canUse() && \YunShop::plugin()->get('dragon-deposit');
    }
}