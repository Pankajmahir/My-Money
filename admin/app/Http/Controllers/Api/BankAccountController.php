<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{

    public function GetBankAccouniDetails(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                 'user_id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),"",401);
            }else{
                $accounts = BankAccount::where('user_id', $request->user_id)->first();
                if(isset($accounts) && $accounts != ""){
                    return ResponseAPI(true,"User Bank Data Found.", "", $accounts, 200 );
                }else{
                    return ResponseAPI(true,'User Bank Data Not Found.',"","",200);
                }
            }
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"","",401);
        }
    }

    public function updateBankAccount(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'user_id' => 'required',
                'account_holder_name' => 'required',
                'ifsc_code' => 'required',
                'account_number' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),"",401);
            }else{
                $accounts = BankAccount::where('user_id', $request->user_id)->first();

                if(isset($accounts) && $accounts != ""){
                    $accounts->account_holder_name = $request->account_holder_name;
                    $accounts->ifsc_code = $request->ifsc_code;
                    $accounts->account_number = $request->account_number;
                    if($accounts->save()){
                        return ResponseAPI(true,"Bank Account updated Succesfull", "", $accounts, 200, 0);
                    }
                }else{
                    $account = new BankAccount;
                    $account->user_id = $request->user_id;
                    $account->account_holder_name = $request->account_holder_name;
                    $account->ifsc_code = $request->ifsc_code;
                    $account->account_number = $request->account_number;
                    if($account->save()){
                        return ResponseAPI(true,"Bank Account added Succesfull", "", $accounts, 200, 0);
                    }
                }
            }                    
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"","",401);
        }
    }

}