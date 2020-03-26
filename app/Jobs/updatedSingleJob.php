<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\SingleReturn\models\ReturnSingleModel;

class updatedSingleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $singleId;
    protected $returnSingledata;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($singleId,$returnSingledata)
    {
        $this->singleId = $singleId;
        $this->returnSingledata = $returnSingledata;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ReturnSingleModel::where('id',$this->singleId)->update($this->returnSingledata);
    }
}
