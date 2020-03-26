<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 15:49
 */

namespace app\backend\modules\charts\modules\member\services;


use app\Jobs\MemberLowerCountJob;
use app\Jobs\MemberLowerOrderJob;

class TimedTaskService
{
    public function handle()
    {
        \Log::debug('----会员下线统计定时任务----');
        set_time_limit(0);

        dispatch(new MemberLowerCountJob());
        dispatch(new MemberLowerOrderJob());
    }

}