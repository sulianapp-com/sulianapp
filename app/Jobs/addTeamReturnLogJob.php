<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Yunshop\TeamReturn\models\TeamReturnLog;
use Yunshop\TeamReturn\services\TimedTaskReturnService;

class addTeamReturnLogJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $teamReturnLogData;
    protected $config;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($teamReturnLogData)
    {
        $this->teamReturnLogData = $teamReturnLogData;
        $this->config = Config::get('income.teamReturn');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logId = TeamReturnLog::insertGetId($this->teamReturnLogData);
        $incomeData = [
            'uniacid' => $this->teamReturnLogData['uniacid'],
            'member_id' => $this->teamReturnLogData['uid'],
            'incometable_type' => $this->config['class'],
            'incometable_id' => $logId,
            'type_name' => $this->config['title'],
            'amount' => $this->teamReturnLogData['amount'],
            'status' => 0,
            'pay_status' => 0,
            'create_month' => date('Y-m'),
            'created_at' => time()
        ];
        (new TimedTaskReturnService())->addIncome($incomeData);
    }
}
