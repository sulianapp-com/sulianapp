<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 11:13
 */

namespace app\backend\modules\charts\modules\order\services;


use app\Jobs\OrderStatisticsJob;

class TimedTaskService
{
    public function handle()
    {
        \Log::debug('商城订单统计');

        set_time_limit(0);

        dispatch(new OrderStatisticsJob());
    }
}