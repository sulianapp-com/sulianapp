<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 11:37
 */

namespace app\backend\modules\charts\modules\phone\listeners;


use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PhoneAttribution
{
    use DispatchesJobs;

    public function handle()
    {
        (new PhoneAttributionService())->phoneStatistics();
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Phone-attribution', '0 1 * * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}