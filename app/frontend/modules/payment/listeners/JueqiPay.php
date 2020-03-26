<?php


namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class JueqiPay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.jueqi_pay_set');

        if (\YunShop::plugin()->get('jueqi-pay') && !is_null($set) && 1 == $set['switch'] && \YunShop::request()->type != 7) {

            $result = [
                'name' => '云支付',
                'value' => '33',
                'need_password' => '0'

            ];
            $event->addData($result);

        }
        return null;
    }

    public function onGetPaymentTypes1(RechargeComplatedEvent $event)
    {
        $set = \Setting::get('plugin.jueqi_pay_set');

        if (\YunShop::plugin()->get('jueqi-pay') && !is_null($set) && 1 == $set['switch'] && \YunShop::request()->type != 7) {

            $result = [
                'name' => '云支付',
                'value' => '33',
                'need_password' => '0'

            ];
            $event->addData($result);
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            GetOrderPaymentTypeEvent::class,
            self::class . '@onGetPaymentTypes'
        );
        $events->listen(
            RechargeComplatedEvent::class,
            self::class . '@onGetPaymentTypes1'
        );
    }
}