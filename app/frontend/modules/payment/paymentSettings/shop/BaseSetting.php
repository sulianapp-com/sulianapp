<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午11:52
 */

namespace app\frontend\modules\payment\paymentSettings\shop;

use app\common\models\Order;
use app\frontend\modules\payment\paymentSettings\PaymentSetting;

abstract class BaseSetting extends PaymentSetting
{
    /**
     * @inheritdoc
     */
    abstract public function exist();

    /**
     * @inheritdoc
     */
    public function getWeight()
    {
        return 30;
    }

    abstract public function canUse();
}