<?php

namespace app\Jobs;

use app\common\models\GoodsCouponQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class addGoodsCouponQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $queueData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($queueData)
    {
        $this->queueData = $queueData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GoodsCouponQueue::insert($this->queueData);
    }
}
