<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\Reminder;
use App\Models\Reminderlogs;
use App\Models\User;
use App\Models\Customer;
use App\Models\Business;
use App\Models\Notification;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class ReminderController extends Controller
{


    public function create(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'business_id' => 'required',
                'customer_id' => 'required',
                'reminder_date'=>'required',
                'reminder_time'=>'required',
                'repeat_type'=>'required',
            ]);

            if($validator->fails())
            {
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $userdata = User::where('id', $request->user_id)->first();
                $oldremider = Reminder::where('business_id',$request->business_id)->where('customer_id',$request->customer_id)->first();

                if($userdata->total_call  > 0 && $userdata->total_message>0){
                    if(isset($oldremider) && $oldremider!=null){
                        $oldremider->reminder_date  = $request->reminder_date;
                        $oldremider->reminder_time  = $request->reminder_time;
                        $oldremider->repeat_type  = $request->repeat_type;
                        $oldremider->frequency  = $request->frequency;
                        $oldremider->every  = $request->every;
                        $oldremider->reminder_type  = $request->reminder_type ?? "";
                        Reminderlogs::where('reminder_id', $oldremider->id)->delete();
                        if($oldremider->save()){
                            $this->Reminderlog($oldremider);
                            return ResponseAPI(true,"Reminder seted succesfull", "", "", 200, 0);
                        }
                    }else{
                        $Reminder = new Reminder;
                        $Reminder->user_id = $request->user_id;
                        $Reminder->customer_id = $request->customer_id;
                        $Reminder->business_id  = $request->business_id;
                        $Reminder->reminder_date  = $request->reminder_date;
                        $Reminder->reminder_time  = $request->reminder_time;
                        $Reminder->repeat_type  = $request->repeat_type;
                        $Reminder->frequency  = $request->frequency;
                        $Reminder->every  = $request->every;
                        $Reminder->reminder_type  = $request->reminder_type ?? "";

                    if($Reminder->save()){
                        $this->Reminderlog($Reminder);
                        return ResponseAPI(true,"Reminder seted succesfull", "", "", 200, 0);
                    }
                }
            }else{
                return ResponseAPI(false,'Please Recharge and Try Again.',"",array(),401);
             }
            }
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
     }



     public function destroy(Request $request)
     {
        try {
             $validator=Validator::make($request->all(),[
                'business_id' => 'required',
                'customer_id' => 'required',
             ]);

             if($validator->fails()){
                 return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
             }else{
                 $Reminderdata = Reminder::where('business_id',$request->business_id)->where('customer_id',$request->customer_id)->first();
                 $Reminder = Reminder::where('business_id',$request->business_id)->where('customer_id',$request->customer_id);
                 Reminderlogs::where('reminder_id', $Reminderdata->id)->delete();
                 if($Reminder->delete()){
                     return ResponseAPI(true,"Reminder deleted Succesfull", "", "", 200);
                 }
             }
        } catch (\Throwable $th) {
             return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
     }

     public function GetReminder(Request $request)
     {
         try {
             $validator=Validator::make($request->all(),[
                'business_id' => 'required',
                'customer_id' => 'required',
             ]);
             if($validator->fails())
             {
                 return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
             }else{
                $getreminder = Reminder::where('business_id',$request->business_id)->where('customer_id',$request->customer_id)->first();
                if(isset($getreminder) && $getreminder!=null){
                    return ResponseAPI(true,"Customer get Succesfull", "", $getreminder, 200);
                }else{
                    return ResponseAPI(false,"Customer get Succesfull", "", $getreminder, 200);
                }

             }

         } catch (\Throwable $th) {
             return ResponseAPI(false,'Something Wrongs.',"",array(),401);
         }
     }

     public function SendReminderCall($remidata)
     {
        try
        {
            $status="fail";
            $TagId="";
            $userdata = User::where('id', $remidata->user_id)->first();

            //get fcm token
            $userToken = DeviceToken::where('user_id', $remidata->user_id)->first();
            if($userdata == Null){
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }

            $customer = Customer::where('id', $remidata->customer_id)->first();
            $business = Business::where('id', $customer['business_id'])->first();

            if($customer->phone == '9999802607' || $customer->phone == '+919999802607'){
                return ResponseAPI(false,'You can not call this number due to being blocked.',"",array(),401);
            }

            if(isset($customer->phone) && isset($customer->name) && isset($customer->balance)){

                $call = SendCall($customer->phone,$customer->name,$customer->balance,$business->bus_name);
                if(isset($call)){
                    $response=json_decode($call);
                    // if(isset($response) && $response->status == "success"){
                    //     $status=$response->status;
                    //     $TagId=$response->TagId;
                    // }
                    if(isset($response) && $response->ERR_CODE == "0"){
                        $status='success';
                        $TagId=$response->CAMPG_ID;
                    }
                }

                if($status == "success"){

                    //Send notification to user
                    $token = "";
                    $token .= $userdata->getdevicetoken->token;

                    $token = rtrim($token, ',');
                    $title = "Your call has been sent to ".$customer->name;
                    $message = date('Y-m-d');

                    SendNotificationUser($token, $message, $title);

                    //  $SERVER_API_KEY = 'AAAACBUmy8g:APA91bGlTz8LDCOrAvjA-as1ORoOtYb8RWkN_sQ1PlOYJ2O4S9uuYTMwsBO1IdqEu4edq59UttOyRRDoYWEf2VqF6RiOwY61mJvmGACojUu3RPvuW9BPS8HzJWUB8Bidj15SUqwTMoGF';

                    //  $data = [
                    //       "to" =>$userToken['token'],
                    //      "notification" => [
                    //          "title" => "Your call has been sent to ".$customer->name,
                    //          "body" => date('Y-m-d'),
                    //      ]
                    //  ];
                    //  $dataString = json_encode($data);

                    //  $headers = [
                    //      'Authorization: key=' . $SERVER_API_KEY,
                    //      'Content-Type: application/json',
                    //  ];

                    //  $ch = curl_init();

                    //  curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                    //  curl_setopt($ch, CURLOPT_POST, true);
                    //  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    //  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    //  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //  curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                    //  $response = curl_exec($ch);

                    //End send notification to user
                    $notification = new Notification;
                    $notification->user_id = $remidata->user_id;
                    $notification->business_id = $remidata->business_id;
                    $notification->customer_id = $remidata->customer_id;
                    $notification->customer_name = $customer->name;
                    $notification->customer_mobile = $customer->phone;
                    $notification->title = "Payment Reminder for ".$customer->name;
                    $notification->description = "Payment Reminder Call Is Pending";
                    $notification->type = 'CALL';
                    $notification->status = 0;
                    $notification->call_id=$TagId;
                    if($notification->save()){
                    // $userdata->total_call-=1;
                        $userdata->save();
                        return ResponseAPI(true,"Notification Send Succesfull", "", $notification, 200);
                    }
                }
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }else{
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
    }


    public function SendReminderSMS($remidata)
    {
        try {
            $status="fail";
            $TagId="";
            $userdata = User::where('id', $remidata->user_id)->first();

            if($userdata == Null){
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }

            $customer = Customer::where('id', $remidata->customer_id)->first();
            $business = Business::where('id', $customer['business_id'])->first();

            if(isset($customer->phone) && isset($customer->name) && isset($customer->balance)){

                if($userdata->total_message  > 0){

                    $call = SendSMS($customer->phone,$customer->name,$business->bus_name,$customer->balance);
                    if(isset($call)){
                        $response=json_decode($call);
                        if(isset($response) && $response->status == "success"){
                            $status=$response->status;
                            $TagId=$response->responseid;
                        }
                    }
                    if($status == "success"){
                        $notification = new Notification;
                        $notification->user_id = $remidata->user_id;
                        $notification->business_id = $remidata->business_id;
                        $notification->customer_id = $remidata->customer_id;
                        $notification->customer_name = $customer->name;
                        $notification->customer_mobile = $customer->phone;
                        $notification->title = "Payment Reminder for ".$customer->name;
                        $notification->description = "Payment Reminder SMS Is Sent";
                        $notification->type = "SMS";
                        $notification->call_id=$TagId;
                        if($notification->save()){
                            $userdata->total_message-=1;
                            $userdata->save();
                            return ResponseAPI(true,"Notification Send Succesfull", "", $notification, 200);
                        }
                    }
                }else{
                    return ResponseAPI(false,'Your SMS balance are oured.',"",array(),401);
                }
            }else{
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
    }

    public function SendReminderEmail($remidata)
    {
        try {
            $status="fail";
            $TagId="";
            $userdata = User::where('id', $remidata->user_id)->first();

            if($userdata == Null){
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }

            $customer = Customer::where('id', $remidata->customer_id)->first();

            $business = Business::where('id', $customer['business_id'])->first();

            if(isset($customer->email) && isset($customer->name) && isset($customer->balance)){

                SendEmail($customer->email,$customer->name,$business->bus_name,$customer->balance);

                $notification = new Notification;
                $notification->user_id = $remidata->user_id;
                $notification->business_id = $remidata->business_id;
                $notification->customer_id = $remidata->customer_id;
                $notification->customer_name = $customer->name;
                $notification->customer_mobile = $customer->phone;
                $notification->title = "Payment Reminder for ".$customer->name;
                $notification->description = "Payment Reminder EMAIL Is Sent";
                $notification->type = "EMAIL";
                $notification->call_id=$TagId ?? "";
                if($notification->save()){
                    return ResponseAPI(true,"Notification Send Succesfull", "", $notification, 200);
                }

            }else{
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
    }

     public function reminder_cron(Request $request)
     {
        $currentHour = now()->format('H');
        if ($currentHour < 9 || $currentHour >= 21) {
            return false;
        }
         try {
            $getreminderlist = Reminderlogs::where('send_date',date('Y-m-d'))
            ->whereRaw(DB::raw("CONCAT(send_date, ' ', send_time) < NOW()"))
            ->get();

            $currentdate = date('Y-m-d');
            $currenttime = date('H');
            if($getreminderlist->count() > 0){

                foreach ($getreminderlist as $key => $value) {
                    if($value->reminder_type == "CALL" || $value->reminder_type == ""){
                        $reminder_id=$value->reminder_id;
                        //dd($value->check_balance($value->user_id));
                        if($value->check_balance($value->user_id) == 1){
                            $remindertime = date('H',strtotime($value->send_time));
                            $logsToday = getTodayNotificationCount($value->user_id, $value->customer_id,'CALL');

                            if ($logsToday < getSetting()->daily_call_limit) {
                                if($currentdate == $value->send_date && $currenttime == $remindertime){
                                    $this->SendReminderCall($value);
                                    logNotification($value->user_id, $value->customer_id,'CALL');
                                }
                            }
                        }
                    }

                    if($value->reminder_type == "SMS" || $value->reminder_type == ""){
                        if($value->check_sms_balance($value->user_id) == 1){
                            $remindertime = date('H',strtotime($value->send_time));
                            $logsToday = getTodayNotificationCount($value->user_id, $value->customer_id,'SMS');
                            if ($logsToday < getSetting()->daily_sms_limit) {
                                if($currentdate == $value->send_date && $currenttime == $remindertime){
                                    $this->SendReminderSMS($value);
                                    logNotification($value->user_id, $value->customer_id,'SMS');
                                }
                            }
                        }
                    }

                    if($value->reminder_type == "EMAIL"){

                        $remindertime = date('H',strtotime($value->send_time));
                        $logsToday = getTodayNotificationCount($value->user_id, $value->customer_id,'EMAIL');
                        if ($logsToday < getSetting()->daily_email_limit) {
                            if($currentdate == $value->send_date && $currenttime == $remindertime){
                                $this->SendReminderEmail($value);
                                logNotification($value->user_id, $value->customer_id,'EMAIL');
                            }
                        }
                    }

                    if($currentdate == $value->send_date && $currenttime == $remindertime){
                     $value->delete();
                    }

                    if(Counterreminder($reminder_id) == 0){
                        $Reminder = Reminder::where('id',$reminder_id)->delete();
                    }
                }
                // return ResponseAPI(true,"Send Succesfull", "", array(), 200);
            }
            // return ResponseAPI(true,'data not found.',"",array(),200);
         } catch (\Throwable $th) {
             return ResponseAPI(false,'Something Wrongs.',"",array(),401);
         }
     }


     function Reminderlog($remidata)
     {

        try {
            if(isset($remidata)){
                if($remidata->repeat_type=="Never"){
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $remidata->reminder_date;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                }else if($remidata->repeat_type=="Hourly"){
                        $remindertime = 24 - date('H',strtotime($remidata->reminder_time));
                        for ($i=0; $i < $remindertime ; $i++) {
                            $timeset = date('H:i:s', strtotime($remidata->reminder_time . " +".$i." hours"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $remidata->reminder_date;
                            $remiderlog->send_time = $timeset;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }
                }else if($remidata->repeat_type=="Daily"){

                        for ($i=0; $i < 7 ; $i++) {
                            $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." days"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $dateset;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }

                }else if($remidata->repeat_type=="Weekly"){

                        for ($i=0; $i < 7 ; $i++) {
                            $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." week"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $dateset;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }

                }else if($remidata->repeat_type=="Monthly"){
                        for ($i=0; $i < 4 ; $i++) {
                            $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." months"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $dateset;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }
                }else if($remidata->repeat_type=="Every 3 Months"){
                        for ($i=0; $i < 4 ; $i++) {
                            $iset=$i;
                            if($i == 1){
                            $iset=3;
                            }else if($i == 2){
                            $iset=6;
                            }else if($i == 3){
                            $iset=9;
                            }
                            $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$iset." months"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $dateset;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }
                }else if($remidata->repeat_type=="Every 6 Months"){
                        for ($i=0; $i < 4 ; $i++) {
                            $iset=$i;
                            if($i == 1){
                            $iset=6;
                            }else if($i == 2){
                            $iset=12;
                            }else if($i == 3){
                            $iset=18;
                            }
                            $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$iset." months"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $dateset;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }
                }else if($remidata->repeat_type=="Yearly"){
                        for ($i=0; $i < 4 ; $i++) {
                            $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." years"));
                            $remiderlog = new Reminderlogs;
                            $remiderlog->reminder_id = $remidata->id;
                            $remiderlog->user_id = $remidata->user_id;
                            $remiderlog->customer_id = $remidata->customer_id;
                            $remiderlog->business_id = $remidata->business_id;
                            $remiderlog->send_date = $dateset;
                            $remiderlog->send_time = $remidata->reminder_time;
                            $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                            $remiderlog->save();
                        }
                }
                else if($remidata->repeat_type=="Custom"){
                    if($remidata->frequency == "Hourly")
                        {
                            $remindertime = 24 - date('H',strtotime($remidata->reminder_time));
                            for ($i=0; $i < $remindertime ; $i++) {
                                $timeset = date('H:i:s', strtotime($remidata->reminder_time . " +".$i." hours"));
                                $remiderlog = new Reminderlogs;
                                $remiderlog->reminder_id = $remidata->id;
                                $remiderlog->user_id = $remidata->user_id;
                                $remiderlog->customer_id = $remidata->customer_id;
                                $remiderlog->business_id = $remidata->business_id;
                                $remiderlog->send_date = $remidata->reminder_date;
                                $remiderlog->send_time = $timeset;
                                $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                                $remiderlog->save();
                            }
                        }else if($remidata->frequency=="Daily"){

                            for ($i=0; $i < 7 ; $i++) {
                                $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." days"));
                                $remiderlog = new Reminderlogs;
                                $remiderlog->reminder_id = $remidata->id;
                                $remiderlog->user_id = $remidata->user_id;
                                $remiderlog->customer_id = $remidata->customer_id;
                                $remiderlog->business_id = $remidata->business_id;
                                $remiderlog->send_date = $dateset;
                                $remiderlog->send_time = $remidata->reminder_time;
                                $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                                $remiderlog->save();
                            }

                }else if($remidata->frequency=="Monthly"){
                    for ($i=0; $i < 4 ; $i++) {
                        $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." months"));
                        $remiderlog = new Reminderlogs;
                        $remiderlog->reminder_id = $remidata->id;
                        $remiderlog->user_id = $remidata->user_id;
                        $remiderlog->customer_id = $remidata->customer_id;
                        $remiderlog->business_id = $remidata->business_id;
                        $remiderlog->send_date = $dateset;
                        $remiderlog->send_time = $remidata->reminder_time;
                        $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                        $remiderlog->save();
                    }
                }
                else if($remidata->frequency=="Yearly"){
                    for ($i=0; $i < 4 ; $i++) {
                        $dateset = date('Y-m-d', strtotime($remidata->reminder_date . " +".$i." years"));
                        $remiderlog = new Reminderlogs;
                        $remiderlog->reminder_id = $remidata->id;
                        $remiderlog->user_id = $remidata->user_id;
                        $remiderlog->customer_id = $remidata->customer_id;
                        $remiderlog->business_id = $remidata->business_id;
                        $remiderlog->send_date = $dateset;
                        $remiderlog->send_time = $remidata->reminder_time;
                        $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                        $remiderlog->save();
                    }
                }else{
                        $remiderlog = new Reminderlogs;
                        $remiderlog->reminder_id = $remidata->id;
                        $remiderlog->user_id = $remidata->user_id;
                        $remiderlog->customer_id = $remidata->customer_id;
                        $remiderlog->business_id = $remidata->business_id;
                        $remiderlog->send_date = $remidata->reminder_date;
                        $remiderlog->send_time = $remidata->reminder_time;
                        $remiderlog->reminder_type  = $remidata->reminder_type ?? "";
                        $remiderlog->save();
                    }
            }
        }
            return ResponseAPI(true,"Stored Reminderlog Succesfull", "", array(), 200);
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
     }


     public function SMSorCALLText(Request $request)
     {
        try {
                   $userdata = User::where('id', $request->user_id)->first();
                    if($userdata == Null){
                        return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                    }
                             $customer = Customer::where('id', $request->customer_id)->first();
                             $business = Business::where('id', $customer['business_id'])->first();
                             if(isset($customer->phone) && isset($customer->name) && isset($customer->balance)){
                                      $amount = str_replace('-', '', $customer->balance);
                                    //   $message="नमस्ते ".$customer->name." आपका ".$userdata->name." पर ".$amount." रुपये बकाया है कृपया जल्द से जल्द इसका भुगतान करे | ".$amount." यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद। टीम Travinities हमारी वेबसाइट पर जाएँ https://makemypayment.co.in/app.html";
                                    //   $call="नमस्ते ".$customer->name." आपका ".$userdata->name." पर ".$amount." रुपये बकाया है कृपया जल्द से जल्द इसका भुगतान करे | ".$amount." यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद।";
                                      $message="नमस्ते ".$customer->name." आपका ".$business->bus_name." पर ".$amount." रुपये बकाया है कृपया जल्द से जल्द इसका भुगतान करे | यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद। टीम MakeMyPayment हमारी वेबसाइट पर जाएँ www.makemypayment.co.in";
                                      $call="नमस्ते ".$customer->name." आपका ".$business->bus_name." पर ".$amount." रुपये बकाया है. कृपया जल्द से जल्द इसका भुगतान करे! यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद।";
                                      $data = array('SMS'=>$message,'CALL'=>$call);
                                      return ResponseAPI(true,'Data found',"",$data,201);
                                 }else{
                                        return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                                 }
                        return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                    } catch (\Throwable $th) {
                        return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }
   }

    public function getaudio($message){
        $txt=$message;
        $txt=htmlspecialchars($txt);
        $txt=rawurlencode($txt);
        return "https://translate.google.com/translate_tts?ie=UTF-8&client=gtx&q=".$txt."&tl=en-IN";
    }

}
