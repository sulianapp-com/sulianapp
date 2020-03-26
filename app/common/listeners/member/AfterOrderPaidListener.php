<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/9
 * Time: 下午3:09
 */

namespace app\common\listeners\member;


use app\backend\modules\member\models\MemberRelation;
use app\common\events\order\AfterOrderPaidEvent;

class AfterOrderPaidListener
{
    public function handle(AfterOrderPaidEvent $event)
    {
        $model = $event->getOrderModel();

        \Log::debug('AfterOrderPaidEvent'.$model->id);

        \Log::debug('推广资格-' . $model->uid);
        // Yy edit:2019-03-06
        MemberRelation::checkOrderPay($model->uid, $model->id);
    }
}