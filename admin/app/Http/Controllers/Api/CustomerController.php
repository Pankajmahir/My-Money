<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Abusive;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'business_id' => 'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                $customer = Customer::where('business_id', $request->business_id)->orderBy('created_at','desc')->paginate(15);
                if($request->page == "-1"){
                $customer = Customer::where('business_id', $request->business_id)->orderBy('created_at','desc')->paginate(100000);
                }
                    return ResponseAPI(true,"Customer get Succesfull", "", $customer, 200);
            }
        } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
        }
    }

    public function create(Request $request)
    {
       try {
            $validator=Validator::make($request->all(),[
                'business_id' => 'required',
                'name'=> 'required',
                'email'=> 'nullable|email',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                
                $nameWords = explode(' ', $request->name);
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
                    return ResponseAPI(false, 'This name is not allowed.', [], [], 400);
                }
                
                $customer = new Customer;
                $customer->business_id = $request->business_id;
                $customer->phone = $request->phone;
                $customer->name = $request->name;
                $customer->email = $request->email;

                if($customer->save()){
                    return ResponseAPI(true,"Customer created Succesfull", "", "", 200);
                }
            }

       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function update(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'name'=> 'required',
                'id'=> 'required',
                'email'=> 'nullable|email',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{
                
                $nameWords = explode(' ', $request->name);
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
                    return ResponseAPI(false, 'This name is not allowed.', [], [], 400);
                }
                
                $customer = Customer::findOrFail($request->id);
                $customer->phone = $request->phone;
                $customer->name = $request->name;
                $customer->email = $request->email;
                if($customer->save()){
                    return ResponseAPI(true,"Customer updated Succesfull", "", "", 200);
                }
            }

       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function destory(Request $request)
    {
       try {
            $validator=Validator::make($request->all(),[
                'id'=>'required',
            ]);

            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{ 
                $customer = Customer::findOrFail($request->id);
                $customer->deleteCustomer($request->id);
                if($customer->delete()){
                    return ResponseAPI(true,"Customer deleted Succesfull", "", "", 200);
                }
            }
       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }

    public function CustomerSearch(Request $request)
    {
       try {
            $customer=array();
            $validator=Validator::make($request->all(),[
                'search'=>'required',
                'business_id'=>'required',
            ]);
            if($validator->fails()){
                return ResponseAPI(false,'required fields',$validator->errors(),array(),401);
            }else{ 
                $search = $request->search;
                $business_id = $request->business_id;
                if(isset($search) && $search!=""){
                  $customer = Customer::where('business_id',$business_id);
                  $customer->where(function ($query) use ($request) {
                    $query->where('name', "like", "%" . $request->search . "%");
                    $query->orWhere('phone', "like", "%" . $request->search . "%");
                });
                  $customer = $customer->get();
                }
                if(isset($customer) && $customer != ""){
                    return ResponseAPI(true,"Customer Found Succesfull", "", $customer, 200);
                }else{
                    return ResponseAPI(true,'No data Found.',"",array(),200);
                }
            }
       } catch (\Throwable $th) {
            return ResponseAPI(false,'Something Wrongs.',"",array(),401);
       }
    }



    //Get customer report data 
    public function getCallDetails(Request $request)
    {

        try {
            // $authorizationHeader = $request->header('Authorization');
            // if (!$authorizationHeader || $authorizationHeader !== "$&^G*D((78@%S87653&^D&df89q4nans89%^$%^E#DdDD&786DD&*F#^(^&DCTD*&9") {
            //     return response()->json([
            //         'success' => false,
            //         'status' => 401,
            //         'message' => "Invalid Authorization Token"
            //     ]);
            //} else 
            if (empty($request->all())) {
              
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'message' => "No Content found"
                ]);
            } else {
                $jsonData = json_encode($request->all(), JSON_PRETTY_PRINT);

                // Determine the file path in the public directory
                $filePath = public_path('home/customer-report.json');
    
                // Ensure the directory exists
                if (!File::isDirectory(public_path('home'))) {
                    File::makeDirectory(public_path('home'), 0755, true, true);
                }
    
                // Store the JSON data in a file in the public directory
                File::put($filePath, $jsonData);
                // return response()->json([
                //     'success' => true,
                //     'status' => 200,
                //     'data' => $request->all()
                // ]);


                $data = $request->all();
                if(!empty($data))
                {


                if($data[0]['OBDOutStatus'] == 'Success' ){
                    // $contactNum = $data[0]['ContactNum'];

                    // if (strpos($contactNum, '0') === 0) {
                    //     $contactNum = str_replace('0', '', $contactNum);
                    // }
                     
                    // $customer = Customer::where('phone', $contactNum)->first();

                    $tagId = $data[0]['TagId'];

                    $notification = Notification::where('call_id', $tagId)->first();
                    if(isset($notification) && !empty($notification['user_id']))
                    {
                        $userdata  = User::where('id', $notification['user_id'])->first();
                    }
                   

                    if (!empty($userdata)) {
                        $userdata->total_call-=1;
                        $userdata->save();
                        return response()->json([
                            'success' => true,
                            'status' => 200,

                        ]);
                    }else{
                        return response()->json([
                            'success' => false,
                            'status' => 200,
                            'message' => "user not found"
                        ]);
                    }

                }else{
                     return response()->json([
                    'success' => true,
                     'status' => 200,
                     'data' => $request->all()
                 ]);
                }
            }
                
            }
        } catch (\Throwable $th) {
          // echo $th->getMessage();
            return ResponseAPI(false, 'Something Wrongs.', "", array(), 401);
        }
    }
    

}