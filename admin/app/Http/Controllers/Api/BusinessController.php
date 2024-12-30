<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;
use App\Models\Abusive;

class BusinessController extends Controller
{
    protected  $newBusiness;
    public function __construct()
    {
        $this->newBusiness = new Business();
    }

    public function GetBusiness(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
            ]);
            if($validator->fails())
            {
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $user = $request->user_id;
                if($user == "" || $user == null){
                    return ResponseAPI(false,'User Not Found!!.',"",array(),401);
                }
                $getuser = Business::where('user_id', $user)->paginate(10);
                if($request->page == "-1"){
                    $getuser = Business::where('user_id', $user)->paginate(10000);
                }

                    return ResponseAPI(true,"Data Found..", "", $getuser);
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

    public function BusinessCreate(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'bus_name' => 'required',
                'user_id' => 'required',
            ]);
        if($validator->fails()){
            return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
        }else{

            // $abusiveName = Abusive::where('name','LIKE',"%{$request->bus_name}%")->first();
            // dd($abusiveName);
            // if ($abusiveName) {
            //     return ResponseAPI(false, 'This business name is not allowed.', [], [], 400);
            // }

            $nameWords = explode(' ', $request->bus_name);
            $isAbusive = false;

            // Check each word against the Abusive table
            foreach ($nameWords as $word) {
                // Trim any extra spaces and perform the query
                $word = trim($word);

                $abusiveName = Abusive::where('name', 'LIKE', '%' . $word . '%')->first();
                
                if ($abusiveName) {
                    $isAbusive = true;
                    break; // Exit loop if an abusive word is found
                }
            }

            // If an abusive word is found, return an error response
            if ($isAbusive) {
                return ResponseAPI(false, 'This business name is not allowed.', [], [], 400);
            }
    
            $business = new Business;
            $business->bus_name = $request->bus_name;
            $business->bus_phone = $request->bus_phone;
            $business->bus_address = $request->bus_address;
            $business->bus_email = $request->bus_email;
            $business->bus_website = $request->bus_website;
            $business->bus_gst = $request->bus_gst;
            $business->user_id = $request->user_id;
            if($business->save()){
                $businesscount = Business::where('user_id', $request->user_id)->count();
                if($businesscount == 1){
                    $user = User::where('id', $request->user_id)->first();
                    $user->default_business = $business->id;
                    $user->save();
                }
                return ResponseAPI(true,"Business created Succesfull", "", "", 200);
            }
        }
       } catch (\Throwable $th) {
           return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function BusinessUpdate(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'bus_name' => 'required',
                'id'=>'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{ 
                $business = Business::findOrFail($request->id);
                    if(isset($business) && $business != ""){
                        
                        $nameWords = explode(' ', $request->bus_name);
                        $isAbusive = false;

                        // Check each word against the Abusive table
                        foreach ($nameWords as $word) {
                            // Trim any extra spaces and perform the query
                            $word = trim($word);

                            $abusiveName = Abusive::where('name', 'LIKE', '%' . $word . '%')->first();
                            
                            if ($abusiveName) {
                                $isAbusive = true;
                                break; // Exit loop if an abusive word is found
                            }
                        }

                        // If an abusive word is found, return an error response
                        if ($isAbusive) {
                            return ResponseAPI(false, 'This business name is not allowed.', [], [], 400);
                        }
                        
                        $business->bus_name = $request->bus_name;
                        $business->bus_phone = $request->bus_phone;
                        $business->bus_address = $request->bus_address;
                        $business->bus_email = $request->bus_email;
                        $business->bus_website = $request->bus_website;
                        $business->bus_gst = $request->bus_gst;
                        // $business->user_id = $request->user_id;
                            if($business->save()){
                                return ResponseAPI(true,"Business updated Succesfull", "", "", 200);
                            }
                    }
            }   

        } catch (\Throwable $th) {
             return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

    public function BusinessDelete(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'id'=>'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{ 
                $business = Business::findOrFail($request->id);
                    $user = User::where('default_business', $request->id)->first();
                    if(isset($user) && $user != ""){
                        return ResponseAPI(false,"Defalut Business can not deleted.", "", "", 401);
                    }else{
                        if(isset($business) && $business != ""){
                            $businessInfo = $this->newBusiness->deleteBusiness($request->id); 
                            if($business->delete()){
                                return ResponseAPI(true,"Business deleted Succesfull", "", "", 200);
                            }
                        }
                    }
                }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

    public function DefaultBusiness(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id'=>'required',
                'business_id'=>'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $user = User::findOrFail($request->user_id);
                $user->default_business = $request->business_id;
                if($user->save()){
                    return ResponseAPI(true,"Default Business updated Succesfull", "", "", 200);
                }
             }
            
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

}