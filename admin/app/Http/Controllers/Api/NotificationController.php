<?php

namespace App\Http\Controllers\Api;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Reminder;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\SendNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    public function getUserData(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'business_id' => 'required',
            ]);
            $notificationarray=array();
            $notification  = Notification::where('user_id',$request->user_id)->where('business_id', $request->business_id)->orderBy('created_at','desc')->get();

            if(count($notification) > 0){
                  foreach ($notification as $key => $value) {

                    $originalDateString = $value['created_at'];
                    $carbonDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $originalDateString);

                    $newFormatString = $carbonDate->format('Y-m-d\TH:i:s.u');

                    $notificationarray[]=array('id'=>$value['id'],
                    'user_id'=>$value['user_id'],'business_id'=>$value['business_id'],'title'=>$value['title'],'description'=>$value['description'],'customer_id'=>$value['customer_id'],'customer_name'=>$value['customer_name'],'customer_mobile'=>$value['customer_mobile'],'type'=>$value['type'],'created_at'=>$newFormatString,'updated_at'=>$value['updated_at']);
                  }
            }




            $notificationfire  = SendNotification::where('user_id',$request->user_id)->orderBy('created_at','desc')->get();
            if(count($notificationfire) > 0){
                foreach ($notificationfire as $key => $value) {
                    $notificationarray[]=array('id'=>$value['id'],'user_id'=>$value['user_id'],'business_id'=>(int)$request->business_id,'title'=>$value['title'],'description'=>$value['message'],'customer_id'=>0,'customer_name'=>"NA",'customer_mobile'=>"NA",'type'=>"NOTI",'created_at'=>$value['created_at'],'updated_at'=>$value['updated_at']);
                  }
            }
            return ResponseAPI(true,"Notification Data Found.", "", $notificationarray, 200);
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong',"",array(),401);
        }
    }

    public function destroy(Request $request)
    {
        try {

            $validator=Validator::make($request->all(),[
                'id' => 'required',
                'type' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                   if($request->type == "NOTI"){
                        $notification = SendNotification::find($request->id);
                            if($notification->delete()){
                                return ResponseAPI(true,"Notification deleted Succesfull", "", "", 200);
                            }
                   }else{
                        $notification = Notification::find($request->id);
                        if($notification->delete()){
                            return ResponseAPI(true,"Notification deleted Succesfull", "", "", 200);
                        }
                   }
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
    }

    public function clear_notification(Request $request)
    {
        try {

            $validator=Validator::make($request->all(),[
                'business_id' => 'required',
                'user_id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                Notification::where('business_id',$request->business_id)->delete();
                SendNotification::where('user_id',$request->user_id)->delete();
                return ResponseAPI(true,"Notification deleted Succesfull", "", "", 200);
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
    }

    public function getNotification(Request $request)
    {

        try {
            $dates =  Reminder::whereDate('reminder_date', date('Y-m-d'))->where('status', 0)->where('reminder_time', date('H:i:s', time()))->get();
            if(isset($dates) && $dates != '[]'){
                foreach($dates as $date){
                    $customer = Customer::where('id', $date->customer_id)->first();
                    $business = Business::where('id', $date->business_id)->first();
                    $user = User::where('id', $business->user_id)->first()->id;
                    $type = 'call';
                    if($type == 'call'){
                        // $call = SendCall();
                    }else{
                        // $call = SendSMS();
                    }

                    $notification = new Notification;
                    $notification->user_id = $user;
                    $notification->business_id = $business->id;
                    $notification->customer_id = $customer->id;
                    $notification->customer_name = $customer->name;
                    $notification->customer_mobile = $customer->phone;
                    $notification->type = 'call';
                    if($notification->save()){
                        return ResponseAPI(true,"Notification Send Succesfull", "", $notification, 200);
                    }
                }
            }else{
                return ResponseAPI(false,'Reminder time and date not Metch.',"",array(),401);
            }
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
        }
    }

    public function SendReminder(Request $request)
    {
        $currentHour = now()->format('H');
        // if ($currentHour < 9 || $currentHour >= 21) {
        //     return ResponseAPI(false, $request->type.' can only be sent between 9 AM and 9 PM.', "", [], 401);
        // }
        // try {
           $status="fail";
           $TagId="";
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'business_id' => 'required',
                'customer_id' => 'required',
                'type' => 'required',
            ]);


            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $userdata = User::where('id', $request->user_id)->first();
                // dd($userdata);
                if($userdata == null){
                    return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                }
                $customer = Customer::where('id', $request->customer_id)->first();
                $business = Business::where('id', $request->business_id)->first();

                if($customer->phone == '9999802607' || $customer->phone == '+919999802607'){
                    return ResponseAPI(false,'You can not call or sms this number due to being blocked.',"",array(),401);
                }

                if(isset($request->type) && $request->type != ""){
                    $logsToday = getTodayNotificationCount($request->user_id, $request->customer_id, $request->type);

                    if($request->type == 'CALL'){
                        if ($logsToday >= getSetting()->daily_call_limit) {
                            return ResponseAPI(false, 'You have reached the daily limit of '.getSetting()->daily_call_limit.' calls for this customer.', "", [], 401);
                        }
                         if($userdata->total_call  > 0){
                            if(isset($customer->phone) && isset($customer->name) && isset($customer->balance)){
                                
                                $call = SendCall($customer->phone,$customer->name,$customer->balance,$business->bus_name);
                                // echo $call. 'test'; exit;
                                    if(isset($call)){
                                        
                                        $response=json_decode($call);
                                        // if(isset($response) && $response->status == "success"){
                                        //     $status=$response->status;
                                        //     $TagId=$response->TagId;
                                        //     // Log::info('User logged in 1.', ['user_id' => $response]);
                                        // }
                                        if(isset($response) && $response->ERR_CODE == "0"){
                                            $status='success';
                                            $TagId=$response->CAMPG_ID;
                                        }
                                    }

                                    if($userdata->total_call  < 6){

                                        //send sms if user call less than 5;
                                        $message="Hey! you have less than 5 Call left please Recharge to continue services. Team MakeMyPayment";
                                        $sms = SendNewCall($customer->phone,$message);
                                        // Log::info('User logged in 2.', ['user_id' => $response]);

                                    }
                                    if($status == "success"){

                                        logNotification($request->user_id, $request->customer_id,$request->type);
                                        //Send notification to user
                                        $token="";
                                        $token.=$userdata->getdevicetoken->token;

                                        $token=rtrim($token, ',');
                                        $title = "Your call has been sent to ".$customer->name;
                                        $message = date('Y-m-d');

                                        // Log::info('User logged in 3.', ['user_id' => $response]);

                                        SendNotificationUser($token,$message,$title);

                                         //End send notification to user
                                        $notification = new Notification;
                                        $notification->user_id = $request->user_id;
                                        $notification->business_id = $request->business_id;
                                        $notification->customer_id = $request->customer_id;
                                        $notification->customer_name = $customer->name;
                                        $notification->customer_mobile = $customer->phone;
                                        $notification->title = "Payment Reminder for ".$customer->name;
                                        $notification->description = "Payment Reminder Call Is Sent";
                                        $notification->type = $request->type;
                                        $notification->status = 1;
                                        $notification->call_id=$TagId;
                                        if($notification->save()){
                                            // $userdata->total_call-=1;
                                            $userdata->save();
                                            return ResponseAPI(true,"Notification Send Succesfull", "", $notification, 200);
                                            // Log::info('User logged in 4.', ['user_id' => $response]);
                                        }
                                    }
                                    // Log::info('User logged in 5.', ['user_id' => $response]);
                                    return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                                    // Log::info('User logged in 8.', ['user_id' => $response]);
                                }else{
                                    // Log::info('User logged in 6.', ['user_id' => $response]);
                                    return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                                }
                            }else{
                                // Log::info('User logged in 7.', ['user_id' => $response]);
                                return ResponseAPI(false,'Please Recharge and Try Again.',"",array(),401);
                            }
                    }else{
                        if ($logsToday >= getSetting()->daily_sms_limit) {
                            return ResponseAPI(false, 'You have reached the daily limit of '.getSetting()->daily_sms_limit.' sms for this customer.', "", [], 401);
                        }
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

                                    if($userdata->total_message  < 6){
                                    //send sms if user sms and call less than 5;
                                    $message="Hey! you have less than 5 Sms left please Recharge to continue services. Team MakeMyPayment";
                                    $sms = SendNewSms($customer->phone,$message);

                                }

                              if($status == "success"){
                                logNotification($request->user_id, $request->customer_id,$request->type);
                                $notification = new Notification;
                                $notification->user_id = $request->user_id;
                                $notification->business_id = $request->business_id;
                                $notification->customer_id = $request->customer_id;
                                $notification->customer_name = $customer->name;
                                $notification->customer_mobile = $customer->phone;
                                $notification->title = "Payment Reminder for ".$customer->name;
                                $notification->description = "Payment Reminder SMS Is Sent";
                                $notification->type = $request->type;
                                $notification->call_id=$TagId;
                                if($notification->save()){
                                    $userdata->total_message-=1;
                                    $userdata->save();
                                    return ResponseAPI(true,"Notification Send Succesfull", "", $notification, 200);
                                }
                              }
                              return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                            }else{
                                return ResponseAPI(false,'Please Recharge and Try Again.',"",array(),401);
                            }
                        }else{
                            return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                        }
                    }
                }
                // Log::info('User logged in 9.', ['user_id' => $response]);
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);

            }
    //    } catch (\Throwable $th) {
    //        return ResponseAPI(false,'Something went Wrong.',"",array(),401);
    //    }
    }


    public function callbackurl(Request $request)
    {
       try {
            $validator=Validator::make($request->all(),[
                'responseid' => 'required',
                'dialstatus' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                 if(isset($request->responseid) && $request->responseid!=""){
                    $notification = Notification::where('call_id',$request->responseid)->first();
                    if($notification){
                        if($notification->status == 0){
                             if($request->dialstatus == "Success"){
                                $notification->description = "Payment Reminder Call Is Accepted";
                             }else{
                                $userdata = User::where('id', $notification->user_id)->first();
                                if($userdata){
                                    $userdata->total_message+=1;
                                    $userdata->save();
                                }
                                $notification->description = "Payment Reminder Call Is ".$request->dialstatus;
                             }
                            $notification->status = 1;
                            $notification->save();
                            return ResponseAPI(true,"Status update successfully", "", array(), 200);
                        }
                    }
                 }
                 return ResponseAPI(false,'Something went Wrong.',"",array(),401);
            }
       } catch (\Throwable $th) {
           return ResponseAPI(false,'Something went Wrong.',"",array(),401);
       }
    }


    //send email notification
    public function send_email_notify(Request $request)
    {
        $currentHour = now()->format('H');
        if ($currentHour < 9 || $currentHour >= 21) {
            return ResponseAPI(false, 'Emails can only be sent between 9 AM and 9 PM.', "", [], 401);
        }

        $emailData  =
        [
            "email" => $request->email,
            "subject"=>$request->subject,
            "content"=>$request->content
        ];

        //check condition email
        if(!empty($request->email))
        {
        try {

            if(isset($request->user_id)){
                $logsToday = getTodayNotificationCount($request->user_id, $request->customer_id, 'EMAIL');
                if ($logsToday >= getSetting()->daily_email_limit) {
                     return ResponseAPI(false, 'You have reached the daily limit of '.getSetting()->daily_email_limit.' emails for this customer.', "", [], 401);
                 }
             }
            // $email=Mail::to($data['email'])->send(new \App\Mail\NotifyRegistrationVerifyMail($data));
             Mail::send('email.email_notification', $emailData, function($message)  use($emailData){
                  $message->to($emailData['email'], 'Makemypayment')
                  // ->cc('makemypayment22@gmail.com')
                  ->subject($emailData['subject']);
                  $message->from('no-reply@makemypayment.co.in','Makemypayment');
               });

                if(isset($request->user_id)){
                   logNotification($request->user_id, $request->customer_id,'EMAIL');
                }
               return ResponseAPI(true,"Email sent successfully", "", array(), 200);
          }catch (\Exception $ex) {
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                // echo $ex->getMessage(); die;
          }
        }
        else
        {
            try {
                // $email=Mail::to($data['email'])->send(new \App\Mail\NotifyRegistrationVerifyMail($data));
                Mail::send('email.email_notification', $emailData, function($message)  use($emailData){
                    $message->to('makemypayment22@gmail.com', 'Makemypayment')
                    ->subject($emailData['subject']);
                    // $message->from('no-reply@makemypayment.co.in','Makemypayment');
                    $message->from('Reminder@makemypayment.co.in','Makemypayment');
                });
                return ResponseAPI(true,"Email sent successfully", "", array(), 200);
              }catch (\Exception $ex) {
                return ResponseAPI(false,'Something went Wrong.',"",array(),401);
                // echo $ex->getMessage(); die;
              }
        }
    }

}
