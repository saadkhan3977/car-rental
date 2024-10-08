<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ride;
use Auth;
use App\Notifications\RideStatusNotification;

class RideController extends Controller
{
    public function index()
    {
        $ride = Ride::with('carinfo','user')->where('status','confirm')->where('rider_id',Auth::user()->id)->first();
        return response()->json(['success'=> true,'message'=>'Ride Info','ride_info'=>$ride],200);
    }
    
    public function rider_ride_update(Request $request,$id)
    {
        $ride = Ride::with('carinfo','rider')->find($id);
        $ride->status = 'confirm';
        $ride->save();

        $user = User::find(Auth::user()->id);
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        $user->save();


        $customer = User::find($ride->user_id); // user ka user model
        $customer->notify(new RideStatusNotification($ride));

        return response()->json(['success'=> true,'message'=>'Ride Info','ride_info'=>$ride],200);
    }
}
