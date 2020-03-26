<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午10:00
 */

namespace app\frontend\modules\payment\orderPayments;

use app\common\models\Order;
use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;

/**
 * 支付设置
 * Class PaymentSetting
 * @package app\frontend\modules\payment
 */
class WebPayment extends BasePayment
{
    /**
     * 满足使用条件
     * @return bool
     */
    public function canUse()
    {

        return parent::canUse() && \YunShop::request()->type != 7;
    }

}