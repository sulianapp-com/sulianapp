<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 11:37
 */

namespace app\backend\modules\charts\modules\team\listeners;


use app\backend\modules\charts\modules\team\services\TeamRankService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TeamRank
{
    use DispatchesJobs;

    public function handle()
    {
        (new TeamRankService())->handle();
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Team-Rank', '0 2 1 * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}