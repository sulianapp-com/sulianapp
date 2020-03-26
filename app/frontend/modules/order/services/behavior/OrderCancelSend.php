<?php

namespace app\frontend\modules\order\services\behavior;

/*
 * 取消发货
 */
use app\common\models\Order;

class OrderCancelSend extends ChangeStatusOperation
{
    protected $statusBeforeChange = [Order::WAIT_RECEIVE];
    protected $statusAfterChanged = Order::WAIT_SEND;
    protected $name = '取消发货';
    protected $time_field = 'cancel_send_time';

    protected $past_tense_class_name = 'OrderCancelSent';


}