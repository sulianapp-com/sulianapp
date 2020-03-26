<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\SingleReturn\models\ReturnSingleTmp;

class TmpUpdateStatusJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $tmpId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tmpId)
    {
        $this->tmpId = $tmpId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ReturnSingleTmp::updateStatusById($this->tmpId,1);
    }
}
