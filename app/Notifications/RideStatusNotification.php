<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RideStatusNotification extends Notification
{
    use Queueable;

    public $rideData;

    public function __construct($rideData)
    {
        $this->rideData = $rideData;
    }

    public function via($notifiable)
    {
        return ['broadcast','database'];
    }

    public function toBroadcast($notifiable)
    {
    
        return new BroadcastMessage([
            'rideData' => $this->rideData,
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'rideData' => $this->rideData
        ];
    }
}
