<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 17:49
 */

namespace app\backend\modules\charts\modules\phone\Jobs;


use app\backend\modules\charts\modules\phone\services\PhoneAttributionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PhoneAttributionJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $phone = (new PhoneAttributionService())->phoneStatistics();
        $phoneModel = new PhoneAttribution();
        foreach ($phone as $item) {

        }
    }
}