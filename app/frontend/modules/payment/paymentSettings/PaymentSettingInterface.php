<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午10:00
 */

namespace app\frontend\modules\payment\paymentSettings;

/**
 * 支付设置
 * Class PaymentSetting
 * @package app\frontend\modules\payment
 */
interface PaymentSettingInterface
{
    /**
     * 开启
     * @return bool
     */
    public function exist();

    /**
     * 满足条件
     * @return bool
     */
    public function canUse();

    /**
     * 获取权重
     * @return int
     */
    public function getWeight();
    // todo 需要支付密码
}