<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 23/02/2017
 * Time: 21:48
 */

namespace app\common\listeners;


use app\common\events\TestFailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventListenerOther implements ShouldQueue
{

    public function __construct()
    {
        //
    }

    public function handle(TestFailEvent $event)
    {
        var_dump($event->messages);

        echo "这是另外的事件!";

        return false;//从这里停止事件的传播
    }

}