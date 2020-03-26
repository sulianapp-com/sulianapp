<?php

namespace app\common\events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProfitEvent
{
    use InteractsWithSockets, SerializesModels;


protected $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array &$data)
    {
        $this->data = &$data;
    }

    public function getDataModel(){
        return $this->data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
