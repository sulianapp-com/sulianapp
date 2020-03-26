<?php

namespace app\Jobs;

use app\backend\modules\charts\modules\member\services\DistributionOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CountCommissionOrderJob implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new DistributionOrderService())->getCommissionOrderNum();
    }
}