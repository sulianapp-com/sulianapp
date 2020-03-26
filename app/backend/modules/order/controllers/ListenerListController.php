<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/20
 * Time: 11:58 AM
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderReceivedImmediatelyEvent;
use Illuminate\Events\Dispatcher;

class ListenerListController extends BaseController
{
    public function index()
    {
        dump('订单创建事件同步监听者');
        dump(app(Dispatcher::class)->getListeners(AfterOrderCreatedImmediatelyEvent::class));
        dump('订单创建事件异步监听者');
        dump(app(Dispatcher::class)->getListeners(AfterOrderCreatedEvent::class));
        dump('订单支付事件同步监听者');
        dump(app(Dispatcher::class)->getListeners(AfterOrderPaidImmediatelyEvent::class));
        dump('订单支付事件异步监听者');
        dump(app(Dispatcher::class)->getListeners(AfterOrderPaidEvent::class));
        dump('订单完成事件同步监听者');
        dump(app(Dispatcher::class)->getListeners(AfterOrderReceivedImmediatelyEvent::class));
        dump('订单完成事件异步监听者');
        dump(app(Dispatcher::class)->getListeners(AfterOrderReceivedEvent::class));
    }
}