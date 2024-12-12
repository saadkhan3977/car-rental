<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('my-channel-{chatId}', function ($user, $chatId) {
    \Log::info('User subscribing to chat channel: ' . $chatId, ['user_id' => $user->id]);
    return true; // or your authorization logic
});


//Broadcast::channel('rider-channel-{riderId}', function ($user, $riderId) {
  //  \Log::info('Rider subscribing to chat channel: ' . $riderId, ['rider_id' => $user->id]);
   // return $user->id === $riderId;
//});

Broadcast::channel('rider-channel-{riderId}', function ($user, $riderId) {
    // Ensure the authenticated user is the rider subscribing to their private channel
	\Log::info('Rider subscribing to chat channel: ');
    return (int) $user->id === (int) $riderId;
});

Broadcast::channel('customer-channel-{customerd}', function ($user, $customerId) {
    \Log::info('Customer subscribing to chat channel: ' );
    // return true; // or your authorization logic
    return (int) $user->id === (int) $customerId;
});

// Broadcast::channel('rider-channel-{riderId}', function ($user, $riderId) {
//     return (int) $user->id === (int) $riderId; // Adjust logic as needed
// });
