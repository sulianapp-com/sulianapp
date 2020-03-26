<?php

namespace app\Jobs;

use app\common\models\CouponLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class addSendCouponLogJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $logData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($logData)
    {
        $this->logData = $logData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CouponLog::insert($this->logData);
    }
}
