<?php

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 下午5:44
 */
class Alipay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        //开启了支付宝支付 并且不是app端
        if (\Setting::get('shop.pay.alipay') && \YunShop::request()->type != 7) {
            $result = [
                'name' => '支付宝',
                'value' => '2',
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