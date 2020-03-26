<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 15:39
 */

namespace app\backend\modules\charts\modules\member\listeners;


use app\backend\modules\charts\modules\member\services\TimedTaskService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MemberLowerListener
{
    use DispatchesJobs;

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('MemberLower', '0 1 * * * *', function () {
                (new TimedTaskService())->handle();
                return;
            });
        });
    }
}