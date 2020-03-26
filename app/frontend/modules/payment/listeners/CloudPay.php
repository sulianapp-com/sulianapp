<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/5
 * Time: 上午4:15
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class CloudPay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.cloud_pay_set');

        if (\YunShop::plugin()->get('cloud-pay') && !is_null($set) && 1 == $set['switch'] && \YunShop::request()->type != 7) {

            $result = [
                'name' => '微信',
                'value' => '6',
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