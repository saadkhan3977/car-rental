<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Queue\SerializesModels;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class RideCreated
{
    use SerializesModels;

    // public $user;
    public $message;

    public function __construct($message)
    {
        // $this->user = $user;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Check if the user is a rider or customer
        if ($this->message->role == 'rider') {
            return new Channel('ride-' . $this->message->rider_id);
        }

        return new Channel('customer-' . $this->message->rider_id);
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }

}
