<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/15
 * Time: 下午3:15
 */

namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class AnotherPay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        //开启了支付宝支付 并且不是app端
        if (\Setting::get('shop.pay.another') && \YunShop::request()->type != 7) {
            $result = [
                'name' => '找人代付',
                'value' => '14',
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