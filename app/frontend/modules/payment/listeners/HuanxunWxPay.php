<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/3
 * Time: 下午5:58
 */

namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class HuanxunWxPay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.huanxun_set');

        if (\YunShop::plugin()->get('huanxun') && !is_null($set) && 1 == $set['switch'] && \YunShop::request()->type != 7) {

            $result = [
                'name' => '微信',
                'value' => '22',
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