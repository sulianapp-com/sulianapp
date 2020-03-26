<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/8
 * Time: 下午1:59
 */

namespace app\common\listeners\member;

use app\backend\modules\member\models\MemberRelation;
use app\common\events\order\AfterOrderCreatedEvent;

class AfterOrderCreatedListener
{
    public function handle(AfterOrderCreatedEvent $event)
    {
        $model = $event->getOrderModel();

        MemberRelation::checkOrderConfirm($model->uid);

    }
}