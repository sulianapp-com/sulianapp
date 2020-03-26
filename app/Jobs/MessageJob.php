<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/23 下午1:56
 * Email: livsyitian@163.com
 */

namespace app\Jobs;


use app\common\events\Event;
use app\common\services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class MessageJob extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        (new MessageService())->push(
            $this->event->member_id,
            $this->event->template_id,
            $this->event->params,
            $this->event->url,
            $this->event->uniacid
        );
    }

}
