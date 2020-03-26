<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/6/25
 * Time: 上午 10:00
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;


class ConvergeAlipayScanPayListener
{
    /**
     * 支付宝支付-HJ
     *
     * @param GetOrderPaymentTypeEvent $event
     * @return null
     */
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.convergePay_set');

        if (\YunShop::plugin()->get('converge_pay') && !is_null($set) && 1 == $set['converge_pay_status'] && 1 == $set['alipay']['alipay_status'] && \YunShop::request()->type != 7) {
            $result = [
                'name' => '支付宝扫码支付(HJ)',
                'value' => '33',
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