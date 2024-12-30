<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Otp;
use App\Models\GeneralSetting;
use App\Models\TransectionSheet;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function getUserInfoByAccessToken(Request $request)
    {
        $token = $request->access_token;

        if($token == "" || $token == null){
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }

        $user = User::where('authToken', $token)->first();

        if ($user == null) {
            return ResponseAPI(false,'User Not Found!!.',"",array(),401);
        }

        return response()->json([
            'status' => true,
            'id' => $user->id,
            'phone' => $user->phone,
        ]);
    }

    public function userUpdate(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'profile' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // 'email'=>'email'
            ]);

            if($validator->fails())
            {
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $user = User::findOrFail($request->user_id);
                $user->name = $request->name;
                $user->email  = $request->email;
                $user->date_of_birth  = $request->date_of_birth;
                $mainpath='uploads/users';
                $folder = public_path($mainpath);
                if($request->hasFile('profile')){
                    if(isset($user->profile) && $user->profile != ""){
                        $path  =  asset($user->profile);
                        if(file_exists($path))
                        {
                           unlink($path);
                        }
                    }
                    $fileName = $user->user_id.date('Ymd').time().'.'.$request->profile->extension();
                    $request->profile->move($folder,$fileName);
                    $user->profile = $mainpath."/". $fileName;
                }
                if($user->save()){
                    return ResponseAPI(true,"User updated Succesfull", "", "", 200, 0);
                }
            }
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
     }

    public function genralSetting()
    {
       try {
            $settings  = GeneralSetting::first(['phone', 'whatsapp_number']);
            return ResponseAPI(true,"Setting Data Found.", "", $settings, 200, );
       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    function getUserDashboard(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'business_id' => 'required',
            ]);

            if($validator->fails())
            {
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{

                $transection = Customer::distinct('tbl_transection_sheets.customer_id')->leftjoin('tbl_transection_sheets', 'tbl_customers.id', '=', 'tbl_transection_sheets.customer_id')->where('tbl_customers.business_id', $request->business_id)->orderBy('tbl_customers.updated_at', 'desc')->get(['tbl_customers.*']);
                // if(isset($transection) && $transection != "" && $transection != "[]"){
                    $transection = $transection;
                    $give = TransectionSheet::where('business_id', $request->business_id)->where('type', 'GIVE')->sum('amount');
                    $got = TransectionSheet::where('business_id', $request->business_id)->where('type', 'GOT')->sum('amount');
                    $data=array('give'=>$give ,'got'=> $got,'transectionlist'=>$transection);
                    return ResponseAPI(true,"Customer Data Found.", "", $data, 200);
                // }else{
                //     return ResponseAPI(true,'Customer data not Founds.',"",array(),200);
                // }
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

    public function DeviceToken(Request $request)
    {
       try {
        $validator=Validator::make($request->all(),[
            'user_id' => 'required',
            'token'=>'required'
        ]);

        if($validator->fails())
        {
            return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
        }else{

            $user = DeviceToken::where('user_id', $request->user_id)->first();

            if(isset($user) && $user != ""){
                $user->delete();
            }


            $user = New DeviceToken;
            $user->user_id = $request->user_id;
            $user->token = $request->token;

            if($user->save()){
                return ResponseAPI(true,"Token inserted Successfully", "", "", 200, 0);
            }

        }
       } catch (\Throwable $th) {
        //    dd($th);
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function ref_check(Request $request)
    {
       try {
                $validator=Validator::make($request->all(),[
                    'user_id' => 'required'
                ]);

                if($validator->fails())
                {
                        return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
                }
                $settings  = GeneralSetting::first();
                $user_id=$request->user_id;
                $user  = User::find($user_id);
                if($user){
                    $flag=false;
                    if(isset($user->referral_by) && $user->referral_by!=null){
                    $flag=true;
                    }
                    $message="Get Rewards By Referral ".$settings->ref_to_call." Calls And ".$settings->ref_to_sms." SMS.";
                    $data=array('Message'=>$message,'flag'=>$flag);
                    return ResponseAPI(true,"Ref Data Found.", "", $data, 200, 0);
                }
                return ResponseAPI(false,'User not found.',"",array(),401);
       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function ref_by(Request $request)
    {
       try {
                $validator=Validator::make($request->all(),[
                    'user_id' => 'required',
                    'referral_code' => 'required'
                ]);

                if($validator->fails())
                {
                    return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
                }
                $settings  = GeneralSetting::first();
                $user_id=$request->user_id;
                $refuser  = User::where('referral_code',$request->referral_code)->first();

                if(!$refuser){
                    return ResponseAPI(false,'Referral User not found.',"",array(),200);
                }
                $user  = User::find($user_id);
                if(!$user){
                    return ResponseAPI(false,'User not found.',"",array(),200);
                }
                if(!isset($user->referral_by) && $user->referral_by==null){
                    $user->total_call+=$settings->ref_to_call;
                    $refuser->total_call+=$settings->ref_from_call;
                    $user->total_message+=$settings->ref_to_sms;
                    $refuser->total_message+=$settings->ref_from_sms;
                    $user->referral_by=$refuser->id;
                    if($user->save() && $refuser->save()){
                        return ResponseAPI(true,"Referral Approved Successfully.", "", array(), 200, 0);
                    }
                }
                return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function sendsamadummy(Request $request)
    {
       try {
          return SendCall("+919904415260","kamlesh","2000","raj");
       } catch (\Throwable $th) {

       }
    }


      //Delete user account Api
      public function delete_account()
      {
          return ResponseAPI(true,"Your data will be deleted in the next 48 hours.",200);
      }

}
