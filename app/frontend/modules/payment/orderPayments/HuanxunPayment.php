<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/6/28
 * Time: 下午5:00
 */

namespace app\frontend\modules\payment\orderPayments;


class HuanxunPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('huanxun');
    }
}