<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 16:05
 */

namespace app\Jobs;


use app\backend\modules\charts\modules\member\services\LowerCountService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MemberLowerCountJob
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        (new LowerCountService())->memberCount();
    }
}