<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransectionSheet;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use File;
class TransectionSheetController extends Controller
{
    public function create(Request $request)
    {
       try {

            $validator=Validator::make($request->all(),[
                'customer_id' => 'required',
                'business_id' => 'required',
                'user_id' => 'required',
                'type' => 'required',
                'amount' => 'required',
                'transaction_date' => 'required',
                'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $transections = new TransectionSheet;
                $transections->customer_id = $request->customer_id;
                $transections->business_id = $request->business_id;
                $transections->user_id = $request->user_id;
                $transections->type = $request->type;
                $transections->amount = $request->amount;
                $transections->note = $request->note;
                $transections->transaction_date = $request->transaction_date;
                $mainpath='uploads/files/' .$transections->business_id."_business_".$request->customer_id;
                $folder = public_path($mainpath);
                if (! File::exists($folder)) {
                    File::makeDirectory($folder);
                }
                if($request->hasFile('file')){
                    $fileName = $transections->business_id.$transections->customer_id.date('Ymd').time().'.'.$request->file->extension();  
                    $request->file->move($folder,$fileName);
                    $transections->file = $mainpath."/". $fileName;
                }
     
                if($transections->save()){
                    $customer = Customer::where('id', $request->customer_id)->first();
                        // if($transections->type == 'GIVE'){
                        //     $customer->balance = $customer->balance  - $transections->amount;
                        //     if($customer->balance < 0){
                        //         $customer->balance_flag = 1;
                        //         $customer->less_balance = abs($customer->balance);
                        //     }
                        // }else{
                        //     $customer->balance = $customer->balance  + $transections->amount;
                        //     $customer->balance_flag = 0;
                        //     $customer->less_balance = 0;
                        // }

                        if($transections->type == 'GIVE'){
                            $customer->balance = $customer->balance  - $transections->amount;
                                if($customer->balance < 0){
                                        $customer->balance_flag = 1;
                                   }
                        }else{
                            $customer->balance = $customer->balance  + $transections->amount;
                             if($customer->balance >= 0){
                                        $customer->balance_flag = 0;
                                   }
                        }
                        $customer->updated_at=date('Y-m-d H:i:s');
                        if($customer->save()){
                            $trans = Transectionsheet::where('id', $transections->id)->first();
                            if($customer->balance < 0){
                                $trans->last_balance = $customer->less_balance;
                                $trans->transection_flag = 1;   
                            }else{
                                $trans->last_balance = $customer->balance;
                                $trans->transection_flag = 0;
                            }
                         
                                if($trans->save()){
                                    return ResponseAPI(true,"Transection created Succesfull", "", "", 200);
                                }
                        }
                }

            }

       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong',"",array(),401);
       }
    }

    public function update(Request $request)
    {
        try {

            $validator=Validator::make($request->all(),[
                'id' => 'required',
                'amount' => 'required',
                'transaction_date' => 'required',
                'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $transections = TransectionSheet::findOrFail($request->id);
                $oldamount = $transections->amount;
                $transections->amount = $request->amount;
                $transections->note = $request->note;
                $transections->transaction_date = $request->transaction_date;
                $mainpath='uploads/files/' .$transections->business_id."_business_".$transections->customer_id;
                $folder = public_path($mainpath);

                if (! File::exists($folder)) {
                    File::makeDirectory($folder);
                }

                if($request->hasFile('file')){
                    if(isset($transections->file) && $transections->file!=""){
                    File::delete($mainpath.'/'.$transections->file);
                    }
                    $fileName = $transections->business_id.$transections->customer_id.date('Ymd').time().'.'.$request->file->extension();  
                    $request->file->move($folder,$fileName);
                    $transections->file = $mainpath."/". $fileName;
                }

                    if($transections->save()){
                        $customer = Customer::where('id', $transections->customer_id)->first();
                            // if($transections->type == 'GOT'){
                            //     $customer->balance = $customer->balance - $fianlvalue;
                            //     if($customer->balance < 0){
                            //         $customer->balance_flag = 1;
                            //         $customer->less_balance = abs($customer->balance) ;
                            //     }
                            // }else{
                            //     $customer->balance = $customer->balance + $fianlvalue;
                            //     $customer->balance_flag = 0;
                            //     $customer->less_balance = 0;
                            // }
                            
                       if($transections->type == 'GIVE'){
                            $customer->balance = $customer->balance + $oldamount;
                            $customer->balance = $customer->balance - $request->amount;
                                if($customer->balance < 0){
                                        $customer->balance_flag = 1;
                                   }
                        }else{
                            $customer->balance = $customer->balance - $oldamount;
                            $customer->balance = $customer->balance  + $request->amount;
                             if($customer->balance >= 0){
                                        $customer->balance_flag = 0;
                                   }
                        }
                        $customer->updated_at=date('Y-m-d H:i:s');
                            if($customer->save()){
                                $trans = Transectionsheet::where('id', $transections->id)->first();
                                if($customer->balance < 0){
                                    $trans->last_balance = $customer->less_balance;
                                    $trans->transection_flag = 1;   
                                }else{
                                    $trans->last_balance = $customer->balance;
                                    $trans->transection_flag = 0;
                                }
                             
                                    if($trans->save()){
                                        return ResponseAPI(true,"Transection updated Succesfull", "", "", 200);
                                    }
                            }
                    }
    
            }

       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong',"",array(),401);
       }
    }

    public function destroy(Request $request)
    {
        try {
            
            $validator=Validator::make($request->all(),[
                'id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $transections = TransectionSheet::findOrFail($request->id);
                $customer = Customer::where('id', $transections->customer_id)->first();
                    // if($transections->type == 'GOT'){
                    //     $customer->balance = $customer->amount - $transections->amount;
                    // }else{
                    //     $customer->balance = $customer->amount + $transections->amount;
                    // }
                    
                     if($transections->type == 'GIVE'){
                            $customer->balance = $customer->balance  + $transections->amount;
                                if($customer->balance < 0){
                                        $customer->balance_flag = 1;
                                   }
                        }else{
                            $customer->balance = $customer->balance  - $transections->amount;
                             if($customer->balance >= 0){
                                        $customer->balance_flag = 0;
                                   }
                        }

                    if($customer->save()){
                        $transections->delete();
                        return ResponseAPI(true,"Transection deleted Succesfull", "", "", 200);
                    }
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong',"",array(),401);
        }
    }

    public function BusinessTransection(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'business_id' => 'required',
                'customer_id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{ 
                    $transections = TransectionSheet::where('business_id', $request->business_id)->where('customer_id', $request->customer_id)->orderBy('created_at','desc')->paginate(15);
                    return ResponseAPI(true,"Bussiness Transection get Successfull", "", $transections, 200);
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong',"",array(),401);
        }
    }


    public function TransectionFilter(Request $request)
    {

        try {
            $validator=Validator::make($request->all(),[
                'business_id' => 'required',
            ]);

            if($validator->fails()){ 
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{

                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $customer_id = $request->customer_id;

                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $transections=array();
                if(isset($start_date) && isset($end_date) && $start_date != "" && $end_date != ""){ 
                    $transections = TransectionSheet::whereBetween('created_at',[$start_date.' 00:00:00', $end_date.' 23:59:59']);
                }

                if(isset($customer_id) && $customer_id != 0){
                    $transections = TransectionSheet::where('customer_id', $customer_id);
                }

                if((isset($customer_id) && $customer_id!=0) || (isset($start_date) && isset($end_date))){
                    $transections=$transections->get();
                }
                return ResponseAPI(true,"Transection get Successfull", "", $transections, 200);
            }

        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something went Wrong',"",array(),401);
        }
    }

}