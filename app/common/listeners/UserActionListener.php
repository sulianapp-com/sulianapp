<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/10
 * Time: 下午12:58
 */

namespace app\common\listeners;


use app\common\events\UserActionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserActionListener  implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  userActionEvent $event
     * @return void
     */
    public function handle(UserActionEvent $event)
    {
        $str = '管理员:' . $event->adminName . '(id:' . $event->uid . ')' . $event->content;

        \Log::info($str);
    }
}