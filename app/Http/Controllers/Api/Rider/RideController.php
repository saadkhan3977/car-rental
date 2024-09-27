<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function index()
    {
        $ride = Ride::with('carinfo','user')->where('status','confirm')->where('rider_id',Auth::user()->id)->first();
        return response()->json(['success'=> true,'message'=>'Ride Info','ride_info'=>$ride],200);
    }
}
