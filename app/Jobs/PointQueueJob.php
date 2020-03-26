<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/4
 * Time: 下午5:48
 */

namespace app\Jobs;


use app\common\models\finance\PointQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class PointQueueJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $uniacid;

    public function __construct($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        $this->uniacid = $uniacid;
    }

    public function handle()
    {
        \YunShop::app()->uniacid = $this->uniacid;
        $queues = $this->getQueues();
        if ($queues->isEmpty()) {
            return;
        }
        foreach ($queues as $queue) {
            PointQueue::returnRun($queue);
        }
    }

    private function getQueues()
    {
        return PointQueue::select()
            ->where('status', PointQueue::STATUS_RUNING)
            ->get();
    }
}