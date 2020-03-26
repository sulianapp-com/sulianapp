<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 23/02/2017
 * Time: 21:37
 */

namespace app\common\events;

use Illuminate\Queue\SerializesModels;

class TestFailEvent extends Event
{
    use SerializesModels;

    public $messages;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($messages=[])
    {
        $this->messages=$messages;
        echo 'TestFailEventFail fire';
        echo "<br/>";
        print_r($messages);

        echo "<br/>";
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}