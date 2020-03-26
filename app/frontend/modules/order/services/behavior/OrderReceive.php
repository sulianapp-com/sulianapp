<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:57
 * comment:订单收货类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderReceivedImmediatelyEvent;
use app\common\models\Order;
use app\Jobs\OrderReceivedEventQueueJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class OrderReceive extends ChangeStatusOperation
{
    use DispatchesJobs;
    protected $statusBeforeChange = [ORDER::WAIT_RECEIVE];
    protected $statusAfterChanged = ORDER::COMPLETE;
    protected $name = '收货';
    protected $time_field = 'finish_time';
    protected $past_tense_class_name = 'OrderReceived';

    protected function _fireEvent()
    {
        $this->fireReceivedEvent();
    }
}