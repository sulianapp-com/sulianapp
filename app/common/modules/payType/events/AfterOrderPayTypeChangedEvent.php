<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/28
 * Time: 下午8:35
 */

namespace app\common\modules\payType\events;

use app\common\events\order\OrderCreatedEvent;

class AfterOrderPayTypeChangedEvent extends OrderCreatedEvent
{

}