<?php

namespace app\Jobs;

use app\common\models\Income;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class addReturnIncomeJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $incomeData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($incomeData)
    {
        $this->incomeData = $incomeData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Income::insert($this->incomeData);
    }
}
