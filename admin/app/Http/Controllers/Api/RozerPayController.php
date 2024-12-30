<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Transection;
use Carbon\Carbon;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Razorpay\Api\Api;

class RozerPayController extends Controller
{

    public function createOrder(Request $request)
    {
        try {

            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'package_id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                
                $key_id = env('RAZOR_KEY');
                $secret = env('RAZOR_SECRET');

                $isTestOrder = $request->is_test_order;
                if($isTestOrder == 'true'){
                    $key_id = env('TEST_RAZOR_KEY');
                    $secret = env('TEST_RAZOR_SECRET');
                }
                $package = Package::findOrFail($request->package_id);
                $amount = $package->price * 100;
                $api = new Api($key_id, $secret);
                $user = User::where('id', $request->user_id)->first();
                $receipt=Transection::count();
                $order = $api->order->create(array('receipt' => (string) $receipt, 'amount' => (string) $amount, 'currency' => 'INR', 'notes'=> array('name'=> $user->name,'contact'=> $user->phone)));
                if(isset($order) && !empty($order)){
                    $transection = new Transection;
                    $transection->order_id = $order->id;
                    $transection->package_id = $request->package_id;
                    $transection->user_id = $request->user_id;
                    $transection->package_name = $package->name;
                    $transection->package_calls = $package->package_calls;
                    $transection->package_message = $package->package_message;
                    $transection->order_status = $order->status;
                    $transection->transection_amount = $package->price;
                    if($transection->save()){
                        return ResponseAPI(true,"Order created successfully.", "", $transection->order_id, 200);
                    }
                }else{
                    return ResponseAPI(false,'Order cannot created',"",array(),401);
                }
            }
            
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
 
    }

    public function createPayment(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'razorpay_signature' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_order_id' => 'required',
                'user_id' => 'required',
            ]);

            $key_id = env('RAZOR_KEY');
            $key_secret = env('RAZOR_SECRET');
            $razorpay_signature = $request->razorpay_signature;
            $razorpay_payment_id = $request->razorpay_payment_id;
            $razorpay_order_id = $request->razorpay_order_id;
            $isTestOrder = $request->is_test_order;
            if($isTestOrder == 'true'){
                $key_id = env('TEST_RAZOR_KEY');
                $key_secret = env('TEST_RAZOR_SECRET');
            }
            
            $generated_signature =  hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $key_secret);
            if($razorpay_signature == $generated_signature){
                $api = new Api($key_id, $key_secret);
                $paymentstatus = $api->payment->fetch($razorpay_payment_id);
                if($paymentstatus->status == 'captured' || $paymentstatus->status == 'authorized'){
                    $transection = Transection::where('order_id', $razorpay_order_id)->first();
                    if(isset($transection) && $transection != ""){
                        $transection->payment_status = 'paid';
                        $transection->payment_id = $paymentstatus->id;
                        if($transection->save()){
                            $user = User::where('id', $transection->user_id)->first();
                            if(isset($user) && $user != ""){
                                $user->total_call += $transection->package_calls;
                                $user->total_message += $transection->package_message;
                                if($user->save()){
                                    return ResponseAPI(true,"Transection updated successfully.", "","", 200);
                                }
                            }
                        }
                    }
                }
                
            }else{
                return ResponseAPI(false,'razorpay signature not matched.',"",array(),401);
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

    public function PaymentHistory(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{ 
                $transection = Transection::select('package_name', 'package_calls', 'package_message', 'transection_amount', 'order_status', 'payment_status', 'created_at')
                                ->where('user_id', $request->user_id)->orderBy('created_at', 'desc')->get();
               if(isset($transection) && $transection != "[]"){
                    return ResponseAPI(true,"Transection data get successfully.", "",$transection, 200);
               }else{
                    return ResponseAPI(false,'Transection data not found.',"",array(),401);
               }
            }
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

}