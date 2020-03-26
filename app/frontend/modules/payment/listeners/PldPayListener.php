<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7
 * Time: 8:57
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class PldPayListener
{
    /**
     * @param RechargeComplatedEvent $event
     * @return null
     */
    public function onGetPaymentTypes(RechargeComplatedEvent $event)
    {

        if (\YunShop::plugin()->get('pld-pay') && app('plugins')->isEnabled('pld-pay')) {

            $result = [
                'name' => 'PLD',
                'value' => '23',
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