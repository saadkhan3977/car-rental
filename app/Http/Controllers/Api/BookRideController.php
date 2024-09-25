<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\RideStatusNotification;
use App\Models\Ride;
use App\Models\User;
use App\Events\RideCreated;
use Pusher\Pusher;
use Auth;

class BookRideController extends BaseController
{
    public function bookRide(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'car_id'=>'required',
            'location_from'=>'required',
            'location_to'=>'required',
            'distance'=>'required',
            'amount'=>'required',
            'pickup_location_lat'=>'required',
            'pickup_location_lng'=>'required',
            'dropoff_location_lat'=>'required',
            'dropoff_location_lng'=>'required',
        ]);

        if($validator->fails()) {
            return response()->json(['success'=>false,'message'=>$validator->errors()],500);    
        }

        $ride = Ride::create([
            'user_id' => Auth::user()->id,
            'car_id' => $request->car_id,
            'location_from' => $request->location_from,
            'location_to' => $request->location_to,
            'amount' => $request->amount,
            'distance' => $request->distance,
            'pickup_location_lat' => $request->pickup_location_lat,
            'pickup_location_lng' => $request->pickup_location_lng,
            'dropoff_location_lat' => $request->dropoff_location_lat,
            'dropoff_location_lng' => $request->dropoff_location_lng,
            'status' => 'pending',
        ]);

        $data = Ride::with('carinfo','rider')->find($ride->id);
        $data['user_info'] = Auth::user();
        // Send a notification to the user
        $admin = User::where('role','admin')->first(); // Admin ka user model
        $admin->notify(new RideStatusNotification($data));
        // broadcast(new RideCreated($data));
        // $this->sendRideNotification($data);

        return $this->sendResponse($ride ,'Ride request sent to admin.',200);

        // return response()->json([
        //     'message' => 'Ride booked successfully!',
        //     'ride' => $ride
        // ], 201);
    }

    protected function sendRideNotification(Ride $ride)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true
            ]
        );

        $data['message'] = 'Your ride from ' . $ride->location_from . ' to ' . $ride->location_to . ' has pending!';
        $data['data'] = $ride;
        $pusher->trigger('ride-channel', 'ride-booked', $data);
    }
}
