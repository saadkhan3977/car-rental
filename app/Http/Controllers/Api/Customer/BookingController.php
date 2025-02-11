<?php

namespace App\Http\Controllers\Api\Customer;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ride;
use App\Models\Car;
use Auth;

class BookingController extends BaseController
{

    public function journey()
    {
        $data['rides'] = Ride::with('rider')->where('user_id',Auth::user()->id)->where('status','complete')->get();
        return $this->sendResponse($data, 'My Journey Lists');
    }

    public function car_list()
    {
        $data = Car::where('status','active')->get();
        return $this->sendResponse($data, 'Car Lists');
    }

    public function ride_history()
    {
        return 'saad';
        $ride = Ride::with('carinfo','user','rider')->where('user_id',Auth::user()->id)->get();
        return response()->json(['success'=> true,'message'=>'Ride Lists','ride_lists'=>$ride],200);
    }
}
