<?php

namespace app\common\listeners;

use app\common\events\ProfitEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProfitEventListener
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
     * @param  ProfitEvent  $event
     * @return void
     */
    public function handle(ProfitEvent $event)
    {
        //
    }
}
