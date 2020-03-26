<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/18
 * Time: 下午3:46
 */

namespace app\Jobs;


use app\common\models\AdminOperationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class AdminOperationLogQueueJob implements ShouldQueue
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
        AdminOperationLog::insert($this->queueData);
    }
}