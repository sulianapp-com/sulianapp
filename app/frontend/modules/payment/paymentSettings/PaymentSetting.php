<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午8:01
 */

namespace app\frontend\modules\payment\paymentSettings;

use app\common\models\OrderPay;

abstract class PaymentSetting implements PaymentSettingInterface
{
    protected $orderPay;
    public function __construct(OrderPay $orderPay)
    {
        $this->orderPay = $orderPay;
    }
}