<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5
 * Time: 15:47
 */

namespace app\backend\modules\charts\listeners;


use app\backend\modules\charts\modules\order\services\TimedTaskService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class OrderStatistics
{
    use DispatchesJobs;

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('OrderStatistics', '0 1 * * * *', function () {
                (new TimedTaskService())->handle();
                return;
            });
        });
    }
}