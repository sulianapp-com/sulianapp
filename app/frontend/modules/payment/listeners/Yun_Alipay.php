<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/6
 * Time: 下午3:29
 */

namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class Yun_Alipay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.yun_pay_set');

        if (\YunShop::plugin()->get('yun-pay') && !is_null($set) && 1 == $set['alipay_switch'] && \YunShop::request()->type != 7) {

            $result = [
                'name' => '支付宝-YZ',
                'value' => '15',
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