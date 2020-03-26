<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 11:06
 */

namespace app\Jobs;


use app\backend\modules\charts\modules\order\services\OrderStatisticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderStatisticsJob
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        (new OrderStatisticsService())->orderStatistics();
    }
}