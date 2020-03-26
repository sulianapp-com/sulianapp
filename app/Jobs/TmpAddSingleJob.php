<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\SingleReturn\models\ReturnSingleModel;

class TmpAddSingleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $tmpSingleData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tmpSingleData)
    {
        $this->tmpSingleData = $tmpSingleData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ReturnSingleModel::insert($this->tmpSingleData);
    }
}
