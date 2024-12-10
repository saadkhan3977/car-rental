<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class RideEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // dd($this->message->rider_id);
        return ['rider-channel-'.$this->message->rider_id];  // Make sure it's public or private as per your use case
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}
