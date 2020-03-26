<?php

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/8
 * Time: 下午5:44
 */
class Alipay_App
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        if (\Setting::get('shop_app.pay.alipay') && \YunShop::request()->type == 7) {
            $result = [
                'name' => '支付宝',
                'value' => '10',
                'need_password' => '0'

            ];
            $event->addData($result);

        }
        return null;
    }


    public function subscribe($events)
    {
        $events->listen(
            GetOrderPaymentTypeEvent::class,
            self::class . '@onGetPaymentTypes'
        );

        $events->listen(
            RechargeComplatedEvent::class,
            self::class . '@onGetPaymentTypes'
        );
    }
}