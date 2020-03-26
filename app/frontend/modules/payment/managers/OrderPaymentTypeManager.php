<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午9:56
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\Order;
use app\common\models\PayType;
use app\frontend\modules\payment\orderPayments\NormalPayment;

/**
 * 订单支付管理者
 * Class OrderPaymentManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentTypeManager extends PaymentTypeManager
{
//    // todo
//    private function getSettingManager()
//    {
//        return app('PaymentManager')->make('OrderPaymentSettingManagers');
//    }
//    // todo
//    private function bindSettings()
//    {
//        // 支付方式集合
//        collect($this->paymentConfig)->each(function ($payment, $code) {
//            /**
//             * 分别绑定支付方式与支付方式设置类. 只定义不实例化,以便于插件在支付方式实例化之前,追加支付方式与支付方式的设置类
//             */
//            // 绑定支付方式
//            $this->bind($code, function (OrderPaymentManager $manager, array $parameter) use ($payment) {
//                /**
//                 * @var OrderPaymentTypeSettingManager $settingManager
//                 * @var Order $order
//                 * @var PayType $payType
//                 */
//                list($order, $payType) = $parameter;
//
//                $settingManager = $this->getSettingManager()->make($payType->code);
//                $settings = $settingManager->getOrderPaymentSettingCollection($order);
//
//                if (isset($payment['payment']) && $payment['payment'] instanceof \Closure) {
//                    return call_user_func($payment['payment'], $payType, $settings);
//                }
//                return new NormalPayment($payType, $settings);
//            });
//
//        });
//    }

}