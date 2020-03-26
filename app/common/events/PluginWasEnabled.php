<?php

namespace app\common\events;

use app\common\services\Plugin;

class PluginWasEnabled extends Event
{
    public $plugin;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

}
