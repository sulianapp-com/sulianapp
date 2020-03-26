<?php

namespace app\Jobs;

use app\common\models\GoodsCouponQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class updateCouponQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $condition;
    protected $updatedData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($condition, $updatedData)
    {
        $this->condition = $condition;
        $this->updatedData = $updatedData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GoodsCouponQueue::updatedData($this->condition, $this->updatedData);
    }
}
