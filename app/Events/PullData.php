<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PullData
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $date;
    public $city;

    /**
     * Create a new event instance.
     *
     * @param $date
     * @param $city
     */
    public function __construct($date, $city)
    {
        $this->date = $date;
        $this->city = $city;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('weather');
    }
}
