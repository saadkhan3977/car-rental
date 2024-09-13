<?php

namespace App\Http\Controllers\Api\Customer;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ride;
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
        $data['rides'] = Car::with('rider')->where('user_id',Auth::user()->id)->where('status','complete')->get();
        return $this->sendResponse($data, 'My Journey Lists');
    }
}
