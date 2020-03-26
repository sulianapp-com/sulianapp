<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/19
 * Time: 16:56
 */

namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class AlipayJsapi
{

    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        //开启了支付宝支付 并且不是app端
        if (\Setting::get('shop.alipay_set') && \YunShop::request()->type != 7) {
            $result = [
                'name' => '支付宝(服务商)',
                'value' => '49',
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