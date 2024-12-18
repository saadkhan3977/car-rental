<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ride;
use App\Models\Conversation;
use App\Models\User;
use App\Events\Customer;
use App\Events\Tracking;
use Auth;
use App\Notifications\RideStatusNotification;
use App\Services\FirebaseService;

class RideController extends Controller
{

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $ride = Ride::with('carinfo','user')->where('status','in process')->where('rider_id',Auth::user()->id)->first();
        return response()->json(['success'=> true,'message'=>'Ride Info','ride_info'=>$ride],200);
    }

    public function rider_ride_update(Request $request,$id)
    {

        $validator = \Validator::make($request->all(),[
            'status'=>'required',
            'lat'=>'required',
            'lng'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['success'=>false,'message'=>$validator->errors()],500);
        }

        $ride = Ride::with('carinfo','rider','user')->find($id);
        if($ride)
        {
            $ride->status = $request->status;
            $ride->save();

            if($request->status == 'reject')
            {
                $admin = User::where('role','admin')->first(); // Admin ka user model
                $admin->notify(new RideStatusNotification($ride));


                $message = [
                    'ride_id' => $ride->id,
                    'rider_id' => $ride->rider_id,
                    'user_id' => $ride->user_id,
                    'text' => 'Ride Request Reject',
                    'createdAt' => $ride->updated_at,
                    'ride_info' => $ride,
                ];

                // Broadcast the event
                broadcast(new Customer((object)$message))->toOthers();

                return response()->json(['success'=> true,'message'=>'Ride Update','ride_info'=>$ride],200);
            }
            else
            {
                $userid = Auth::id();
                $chat = Conversation::where(function ($query) use ($userid) {
                    $query->where('user_id', $userid);
                        // ->where('target_id', $targetid);
                })->orWhere(function ($query) use ($userid) {
                    $query->where('target_id', $userid);
                        // ->where('target_id', Auth::id());
                })->first();

                if(!$chat)
                {
                    Conversation::create([
                        'user_id' => $userid,
                        'target_id' => $ride->user_id,
                    ]);
                }

                $user = User::find(Auth::user()->id);
                $user->lat = $request->lat;
                $user->lng = $request->lng;
                $user->save();

                $customer = User::find($ride->user_id); // user ka user model

                // $customer->notify(new RideStatusNotification($ride));
                // $rider = User::find($request->rider_id); // rider ka user model

                $body = Auth::user()->first_name . ' ' . Auth::user()->last_name .' Accept Your Ride Request';
                $title = request()->text;
                $fcmToken = $customer->device_token;
                $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);
                $ridee = Ride::with('carinfo','rider','user')->find($id);


                $message = [
                    'ride_id' => $ridee->id,
                    'rider_id' => $ridee->rider_id,
                    'user_id' => $ridee->user_id,
                    'text' => 'Ride Request Accept',
                    'createdAt' => $ridee->updated_at,
                    'ride_info' => $ridee,
                ];

                // Broadcast the event
                broadcast(new Customer((object)$message))->toOthers();

                return response()->json(['success'=> true,'message'=>'Ride Update','ride_info'=>$ridee],200);
            }
        }
        else
        {
            return response()->json(['success'=> false,'message'=>'No Ride Found.'],404);
        }
    }

    public function ride_on_the_way(Request $request,$id)
    {
        $ride = Ride::with('carinfo','rider','user')->find($id);
        if($ride)
        {
            $ride->status = $request->status;
            $ride->save();

            $ridee = Ride::with('carinfo','rider','user')->find($id);


            return response()->json(['success'=> true,'message'=>'Ride Update','ride_info'=>$ridee],200);
        }
        else
        {
            return response()->json(['success'=> false,'message'=>'No Ride Found.'],404);
        }
    }

    public function update_location(Request $request,$id)
    {

        $user = User::find(Auth::user()->id);
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        $user->save();

        $ride = Ride::with('carinfo','rider','user')->find($id);

        $message = [
            'ride_id' => $ride->id,
            'rider_id' => $ride->rider_id,
            'user_id' => $ride->user_id,
            'text' => 'Rider Location Update',
            'createdAt' => $ride->updated_at,
            'data' => ['lat' =>$request->lat, 'lng' => $request->lng],
        ];

        broadcast(new Tracking((object)$message))->toOthers();

        return response()->json(['success'=> true,'message'=>'Ride Update','ride_info'=>$ride],200);
    }
}
