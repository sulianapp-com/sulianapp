<?php

namespace app\common\events;

use Illuminate\Routing\Router;

class ConfigureRoutes extends Event
{
    public $router;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
}
