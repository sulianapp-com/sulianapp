<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/12/27
 * Time: 下午3:37
 */

namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class DianbangScan
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        //开启了支付宝支付 并且不是app端
        if (\Setting::get('plugin.dian-bang-scan') && \YunShop::request()->type != 7) {
            $result = [
                'name' => '店帮扫码',
                'value' => '24',
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