<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/7/30
 * Time: 15:23
 */

namespace app\Jobs;


use app\common\facades\Setting;
use Illuminate\Contracts\Bus\Dispatcher;

class DispatchesJobs
{
    const LOW = 'low';

    public static function dispatch($job, $queue)
    {
        $is_open = Setting::getNotUniacid('supervisor.queue.is_classify');
        if ($is_open) {
            $job->queue = $queue;
        }
        return app(Dispatcher::class)->dispatch($job);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param  mixed  $job
     * @return mixed
     */
    public function dispatchNow($job)
    {
        return app(Dispatcher::class)->dispatchNow($job);
    }
}