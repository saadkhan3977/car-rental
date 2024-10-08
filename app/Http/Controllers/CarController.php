<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Ride;
use App\Models\User;
use Pusher\Pusher;
use App\Events\RideStatus;
use App\Notifications\RideStatusNotification;

// use App\Models\Category;
// use App\Models\PostTag;
// use App\Models\Brand;

use Illuminate\Support\Str;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars=Car::paginate(10);
        // return $cars;
        return view('backend.car.index')->with('cars',$cars);
    }
    
    public function car_ride_request()
    {
        $cars= Ride::where('status','pending')->paginate(10);
        // return $cars;
        return view('backend.car.ride-request')->with('rides',$cars);
    }
    
    public function car_rides()
    {
        $cars= Ride::whereNot('status','pending')->paginate(10);
        // return $cars;
        return view('backend.car.ride-list')->with('rides',$cars);
    }
    
    public function car_ride_assign_form($id)
    {
        $data['ride'] = Ride::find($id);
        $data['cars'] = Car::get();
        $data['riders'] = User::where('role','rider')->where('assign','no')->get();
        return view('backend.car.ride-assign',$data);
    }
    
    public function car_ride_assign($id,Request $request)
    {
        // return $request->rider_id;
        // return $request->all();
        $ride =  Ride::find($id);
        $ride->rider_id = $request->rider_id;
        $ride->status = 'in process';
        $ride->save();

        // $admin->notify(new RideStatusNotification($data));
        // $this->sendRideNotification($ride);

        $data = Ride::with('carinfo','rider')->find($id);

        $rider = User::find($request->rider_id); // rider ka user model
        $rider->notify(new RideStatusNotification($data));
        
        // $user = User::find($ride->user_id); // user ka user model
        // $user->notify(new RideStatusNotification($data));

        // broadcast(new MessageSent((object)$message))->toOthers();

        // $data['cars'] = Car::get();
        // $data['riders'] = User::where('role','rider')->where('assign','no')->get();
        return redirect('admin/car-ride-new')->with('success' , 'Ride Assign Successfully');
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

        $data['message'] = 'Your ride from ' . $ride->location_from . ' to ' . $ride->location_to . ' has been booked!';
        $data['data'] = $ride;
        $pusher->trigger('ride-channel', 'ride-booked', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.car.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'string|required',
            'model'=>'required',
            'no'=>'required',
            'seats'=>'required',
            'image'=>'string|required',
            'price'=>'numeric',
            'status'=>'required|in:active,inactive',
        ]);

        $data=$request->all();

        $status=Car::create($data);
        if($status){
            request()->session()->flash('success','Car Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('car.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $car=Car::findOrFail($id);
        return view('backend.car.edit')->with('car',$car);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $car=Car::findOrFail($id);
        $this->validate($request,[
            'name'=>'string|required',
            'model'=>'required',
            'no'=>'required',
            'seats'=>'required',
            'image'=>'string|required',
            'price'=>'numeric',
            'status'=>'required|in:active,inactive',
        ]);

        $data=$request->all();
        
        // return $data;
        $status=$car->fill($data)->save();
        if($status){
            request()->session()->flash('success','Car Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('car.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $status=$product->delete();
        
        if($status){
            request()->session()->flash('success','Product successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting product');
        }
        return redirect()->route('product.index');
    }
}
