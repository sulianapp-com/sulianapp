<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/8
 * Time: 下午2:01
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\OrderPay;
use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;
use app\frontend\modules\payment\paymentSettings\PaymentSettingInterface;
use Illuminate\Container\Container;

class OrderPaymentTypeSettingManager extends Container
{
    public function getOrderPaymentSettingCollection($code,OrderPay $orderPay){

        $settings = $this->make($code,[$orderPay]);

        $settings = collect($settings)->map(function($setting) use ($orderPay){
            return call_user_func($setting,$orderPay);
        });
        $settings = $settings->filter(function(PaymentSettingInterface $setting){
            return $setting->exist();
        });
        return new OrderPaymentSettingCollection($settings);
    }
}