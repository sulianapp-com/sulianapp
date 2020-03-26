<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/2
 * Time: 下午1:40
 */

namespace app\frontend\modules\payment\orderPayments;


use app\common\models\PayType;
use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;

class AppPayment extends BasePayment
{

    public function canUse()
    {
        return parent::canUse() && \YunShop::request()->type == 7;
    }
}