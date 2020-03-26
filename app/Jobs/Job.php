<?php

namespace app\Jobs;

use app\common\events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\SingleReturn\models\TestQuery;

class Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5;

    public $timeout = 120;


    protected $event;


    public function __construct(Event $event)
    {
        $this->event = $event;
    }


}
