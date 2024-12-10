<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Auth;

class RideCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
