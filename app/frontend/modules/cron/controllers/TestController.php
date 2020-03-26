<?php
namespace app\frontend\modules\cron\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/5/16
 * Time: 15:00
 */
class TestController extends BaseController
{

    public function run() {
        \Artisan::call('schedule:run');
        Log::info('处理任务');
    }
}