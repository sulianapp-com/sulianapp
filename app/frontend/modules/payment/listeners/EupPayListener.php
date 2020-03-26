<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/7
 * Time: 下午4:07
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class EupPayListener
{
    /**
     * @param RechargeComplatedEvent $event
     * @return null
     */
    public function onGetPaymentTypes(RechargeComplatedEvent $event)
    {

        if (\YunShop::plugin()->get('eup-pay') && app('plugins')->isEnabled('eup-pay')) {

            $result = [
                'name' => 'EUP',
                'value' => '19',
                'need_password' => '0'

            ];
            $event->addData($result);

        }
        return null;
    }


    /**
     * @param RechargeComplatedEvent $event
     */
    public function subscribe($events)
    {
//        $events->listen(
//            GetOrderPaymentTypeEvent::class,
//            self::class . '@onGetPaymentTypes'
//        );

        $events->listen(
            RechargeComplatedEvent::class,
            self::class . '@onGetPaymentTypes'
        );
    }
}