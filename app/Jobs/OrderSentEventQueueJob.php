<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/18
 * Time: 下午3:46
 */

namespace app\Jobs;

use app\common\events\order\AfterOrderSentEvent;
use app\common\facades\Setting;
use app\common\models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class OrderSentEventQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Order
     */
    protected $order;
    protected $orderId;

    /**
     * OrderReceivedEventQueueJob constructor.
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;

        $this->order = Order::find($orderId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            \YunShop::app()->uniacid = $this->order->uniacid;
            Setting::$uniqueAccountId = $this->order->uniacid;
            if(!$this->order->orderSentJob){
                \Log::error('订单收货事件触发失败',"{$this->orderId}未找到orderReceivedJob记录");
                return;
            }
            if($this->order->orderSentJob->status == 'finished'){
                return;
            }
            $this->order->orderSentJob->status = 'finished';
            $this->order->orderSentJob->save();
            event(new AfterOrderSentEvent($this->order));

        });
    }
}