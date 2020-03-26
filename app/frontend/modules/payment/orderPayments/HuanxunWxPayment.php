<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/3
 * Time: 下午6:12
 */

namespace app\frontend\modules\payment\orderPayments;


class HuanxunWxPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('huanxun');
    }
}