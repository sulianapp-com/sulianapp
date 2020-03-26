<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:49
 */

namespace app\common\listeners\member;

use app\backend\modules\member\models\MemberRelation;
use app\common\events\order\AfterOrderReceivedEvent;

class AfterOrderReceivedListener
{
    public function handle(AfterOrderReceivedEvent $event)
    {
        \Log::debug('AfterOrderReceivedListener');

        $model = $event->getOrderModel();

        // Yy edit:2019-03-06
        MemberRelation::checkOrderFinish($model->uid, $model->id);
    }
}