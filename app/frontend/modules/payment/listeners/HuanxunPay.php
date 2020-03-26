<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/6/28
 * Time: 下午4:03
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;


class HuanxunPay
{

    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.huanxun_set');

        if (\YunShop::plugin()->get('huanxun') && !is_null($set) && 1 == $set['quick_switch'] && \YunShop::request()->type != 7) {

            $result = [
                'name' => '银联快捷支付',
                'value' => '18',
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