<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\SendNotification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class SendNotificationController extends Controller
{

    public function index()
    {
        $notifications = SendNotification::orderby('created_at', 'desc')->paginate(10);
        return view('send_notifications.index', compact('notifications'));
    }

    public function create(Type $var = null)
    {
       return view('send_notifications.create');
    }

    public function store_old( Request $request)
    {
        try {
            $validated = $request->validate([
                'user' => 'required',
                'title' => 'required',
                'message' => 'required',
            ]);
             $title = $request->title;
             $message =$request->message;
            $token="";
            if($request->user == "all_user"){
               $userdata = User::where('status',1)->where('user_type','customer')->get();
               foreach ($userdata as $key => $value) {

                  if(isset($value->getdevicetoken->token) && $value->getdevicetoken->token!=""){
                    $notifications = new SendNotification;
                    $notifications->user_id = $value->id;
                    $notifications->title = $title;
                    $notifications->message = $message;
                    $notifications->save();
                    $token.=$value->getdevicetoken->token.",";
                  }
               }
            }else{
                $userdata = User::find($request->user);
                if(isset($userdata) && $userdata->status==1 && isset($userdata->getdevicetoken->token)){
                    $notifications = new SendNotification;
                    $notifications->user_id = $request->user;
                    $notifications->title = $title;
                    $notifications->message = $message;
                    $notifications->save();
                    $token.=$userdata->getdevicetoken->token;
                }
            }
            $token=rtrim($token, ',');
            //SendNotificationUser($token,$message,$title);
            toastr()->success('Send notification successfully!');
            return redirect()->route('send-notifications.index');
        } catch (\Throwable $th) {
            toastr()->error('something went wrong');
            return redirect()->route('send-notifications.index');
        }

    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user' => 'required',
                'title' => 'required',
                'message' => 'required',
            ]);

            $title = $request->title;
            $message = $request->message;

            $token = "";
            if ($request->user == "all_user") {
                // Retrieve users in chunks of 500
                User::where('status', 1)
                    ->where('user_type', 'customer')
                    ->with('getdevicetoken') // eager load device token relationship
                    ->chunk(500, function ($users) use (&$token, $title, $message) {
                        $tokens = [];

                        foreach ($users as $user) {
                            if (isset($user->getdevicetoken->token) && !empty($user->getdevicetoken->token)) {
                                $tokens[] = $user->getdevicetoken->token;

                                // Save the notification to the database
                                $notifications = new SendNotification;
                                $notifications->user_id = $user->id;
                                $notifications->title = $title;
                                $notifications->message = $message;
                                $notifications->save();
                            }
                        }

                        // Combine tokens and send notification in batches
                        if (!empty($tokens)) {
                            $tokenString = implode(',', $tokens);
                            // Send notification to this chunk of users
                            SendNotificationUser($tokenString, $message, $title);
                        }
                    });
            } else {
                // If specific user is selected
                $user = User::find($request->user);
                if (isset($user) && $user->status == 1 && isset($user->getdevicetoken->token)) {
                    $notifications = new SendNotification;
                    $notifications->user_id = $user->id;
                    $notifications->title = $title;
                    $notifications->message = $message;
                    $notifications->save();

                    $token = $user->getdevicetoken->token;

                    // Send notification to the selected user
                    SendNotificationUser($token, $message, $title);
                }
            }

            toastr()->success('Notification sent successfully!');
            return redirect()->route('send-notifications.index');

        } catch (\Throwable $th) {
            toastr()->error('Something went wrong');
            return redirect()->route('send-notifications.index');
        }
    }

    public function destroy(Request $request)
    {
        $notifications = SendNotification::where('id', $request->id)->first();
        if($notifications->delete()){
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

}
