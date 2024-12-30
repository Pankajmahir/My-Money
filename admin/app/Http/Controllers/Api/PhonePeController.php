<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Http;
use App\Models\PhonepayTransaction;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
// use Curll;
class PhonePeController extends Controller
{


        //transactions initiate api
        public function create_transactions(Request $request)
        {
            $decodedData = $request->json()->all();
            $merchantId = 'TRAVONLINE';
            // Generate a unique identifier for the transaction (you might use a better method)
            $uniqueTransactionId = uniqid();


            // Concatenate the merchant ID and the unique transaction ID
            $transactionId = $merchantId . $uniqueTransactionId;

            $storePostData = [
                'merchantId' => isset($merchantId) ? $merchantId : "",
                'merchantTransactionId' => isset($transactionId) ? $transactionId : "",
                'merchantUserId' => isset($decodedData) ? $decodedData['merchantUserId'] : "",
                'amount' => isset($decodedData) ? $decodedData['amount'] : "",
                'mobileNumber' => isset($decodedData) ? $decodedData['mobile_number'] : "",
            ];

            $amount = isset($decodedData) ? $decodedData['amount'] : "0.00";
            // Remove digits after the decimal point
            $decimalPart = intval($amount);
            
            $storePostData2 = [
                'merchantId' => isset($merchantId) ? $merchantId : "",
                'merchantTransactionId' => isset($transactionId) ? $transactionId : "",
                'merchantUserId' => isset($decodedData) ? $decodedData['merchantUserId'] : "",
                'amount' =>$decimalPart."00",
                'mobileNumber' => isset($decodedData) ? $decodedData['mobile_number'] : "",
            ];

            PhonepayTransaction::create($storePostData);

            return response()->json(['status' => 200, 'result' => $storePostData2]);
        }
//phone pay call back url by rahul
    public function handleCallback(Request $request)
    {
        try{
                $jsonData = $request->json()->all();
                $jsonData2 = 'hello';
                $dataStringss = json_encode($jsonData2);
                $filePath = storage_path('app/call-bck.txt');
                file_put_contents($filePath, $jsonData2, FILE_APPEND);
        
             // Decode the base64 data
                $jsonString = base64_decode($jsonData['response']);

                // Parse the JSON string into a PHP object
                $decodedData = json_decode($jsonString);
            
            
                $merchantId  = isset($decodedData)? $decodedData->data->merchantId : "";
                $merchantTransactionId  =   isset($decodedData) ? $decodedData->data->merchantTransactionId : "";
                $storePostData = array(
                    'post_response' =>$jsonData['response']
                    //'get_response'=>
                );
                PhonepayTransaction::where('merchantTransactionId',$merchantTransactionId)->update($storePostData);
                
                $this->checkStatus($merchantTransactionId);
            
        } catch (Exception $e) {
            // Handle exceptions, e.g., connection issues, timeouts, etc.
            return response()->json(['error' => 'API request failed: ' . $e->getMessage()], 500);
        }
        
    }


    public function checkStatus()
    {
        
       try{
            $saltKey = '6fbca744-423f-4ebe-93ab-0882f48f7b9b';
            $saltIndex = 1;
            $merchantTransactionId  =   $_GET['merchantTransactionId'];
            

            $string = '/v3/transaction/TRAVONLINE/'.$merchantTransactionId.'/status'.$saltKey;
            
            $sha256 = hash('sha256',$string);
        
        
            // Make the API request
            $finalXHeader = $sha256.'###'.$saltIndex;
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://mercury-t2.phonepe.com/v3/transaction/TRAVONLINE/'.$merchantTransactionId.'/status', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $finalXHeader,
                    'accept' => 'application/json',
                ],
            ]);

            $result   =    $response->getBody()->getContents();
        
            $resultarr = json_decode($result);
            if(isset($resultarr) && $resultarr->data->paymentState == 'PENDING')
            {
                $storeGetData = array(
                    'flag'=>1,
                    'status' =>isset($resultarr) ? $resultarr->data->paymentState: ""
                );
            }elseif(isset($resultarr) && $resultarr->data->paymentState == 'COMPLETED'){
                $storeGetData = array(
                    'status' =>isset($resultarr) ? $resultarr->data->paymentState: ""
                );
               
                $userData   =   PhonepayTransaction::where('merchantTransactionId',$merchantTransactionId)->latest()->first();
                if(isset($userData) && !empty($userData))
                {
                    $getUserData = User::select('*')->where(['id'=>$userData['merchantUserId']])->latest()->first();
                    
                    $totalCalls = $getUserData['total_call'];
                    $total_message = $getUserData['total_message'];
                }

                $amount = isset($resultarr->data->amount) ? number_format($resultarr->data->amount, 2, '.', '') : '0.00';
                
                $amount = $amount/100;
               
                $package = Package::where(['status'=>1,'price'=>$amount])->first();
           
               
               if(isset($package) && !empty($package))
               {
                $packageMessage =   $package['package_message'];
                $packageCalls   =  $package['package_calls'];
               }
               else{
                $packageMessage =   0;
                $packageCalls   =  0;
               }
                

                $updateUsersPackages    = array(

                    'total_call'=>$packageCalls+$totalCalls,
                    'total_message'=>$packageMessage+$total_message

                );  
                User::where('id',$userData['merchantUserId'])->update($updateUsersPackages);


            }
            else{
                $storeGetData = array(
                    'status' =>isset($resultarr) ? $resultarr->data->paymentState: ""
                );
            }

            PhonepayTransaction::where('merchantTransactionId',$merchantTransactionId)->update($storeGetData);
        
            if ($response->getStatusCode()==200) {
                // Process the response data as needed
                return response()->json($resultarr);
            } else {
                // Handle the case where the request was not successful
                return response()->json(['error' => 'API request failed'], $response->getStatusCode());
            }
    }catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle exceptions, e.g., connection issues, timeouts, etc.
            return response()->json(['error' => 'API request failed: ' . $e->getMessage()], 500);
        }

    }


     //check status pending 
     public function checkPaymentStatus()
     {
         
        try{
 
             $users = PhonepayTransaction::select('*')->where('flag','1')->paginate(50);
            
             foreach ($users as $user) {
                 $merchantTransactionId  = $user['merchantTransactionId'];
                 
             
                 $saltKey = '6fbca744-423f-4ebe-93ab-0882f48f7b9b';
                 $saltIndex = 1;
                 //$merchantTransactionId  =   'TRAVONLINE_655c5503a65d1';
                 $string = '/v3/transaction/TRAVONLINE/'.$merchantTransactionId.'/status'.$saltKey;
                 
                 $sha256 = hash('sha256',$string);
             
             
                 // Make the API request
                 $finalXHeader = $sha256.'###'.$saltIndex;
                 $client = new \GuzzleHttp\Client();
                 $response = $client->request('GET', 'https://mercury-t2.phonepe.com/v3/transaction/TRAVONLINE/'.$merchantTransactionId.'/status', [
                     'headers' => [
                         'Content-Type' => 'application/json',
                         'X-VERIFY' => $finalXHeader,
                         'accept' => 'application/json',
                     ],
                 ]);
 
            $result   =    $response->getBody()->getContents();
         
             $resultarr = json_decode($result);
             if(isset($resultarr) && $resultarr->data->paymentState == 'PENDING')
            {
                $storeGetData = array(
                    'flag'=>1,
                    'status' =>isset($resultarr) ? $resultarr->data->paymentState: ""
                );
            }elseif(isset($resultarr) && $resultarr->data->paymentState == 'COMPLETED'){
                $storeGetData = array(
                    'status' =>isset($resultarr) ? $resultarr->data->paymentState: ""
                );
               
                $userData   =   PhonepayTransaction::where('merchantTransactionId',$merchantTransactionId)->latest()->first();
                if(isset($userData) && !empty($userData))
                {
                    $getUserData = User::select('*')->where(['id'=>$userData['merchantUserId']])->latest()->first();
                    
                    $totalCalls = $getUserData['total_call'];
                    $total_message = $getUserData['total_message'];
    
                   
                }

                $amount = isset($resultarr->data->amount) ? number_format($resultarr->data->amount, 2, '.', '') : '0.00';

                $package = Package::where(['status'=>1,'price'=>$amount])->first();
               
               if(isset($package) && !empty($package))
               {
                $packageMessage =   $package['package_message'];
                $packageCalls   =  $package['package_calls'];
               }
               else{
                $packageMessage =   0;
                $packageCalls   =  0;
               }
                

                $updateUsersPackages    = array(

                    'total_call'=>$packageCalls+$totalCalls,
                    'total_message'=>$packageMessage+$total_message

                );  
                User::where('id',$userData['merchantUserId'])->update($updateUsersPackages);


            }
            else{
                $storeGetData = array(
                    'status' =>isset($resultarr) ? $resultarr->data->paymentState: ""
                );
            }
 
             PhonepayTransaction::where('merchantTransactionId',$merchantTransactionId)->update($storeGetData);
         
             if ($response->getStatusCode()==200) {
                 // Process the response data as needed
                 return response()->json($resultarr);
             } else {
                 // Handle the case where the request was not successful
                 return response()->json(['error' => 'API request failed'], $response->getStatusCode());
             }
         }
     }catch (\GuzzleHttp\Exception\RequestException $e) {
             // Handle exceptions, e.g., connection issues, timeouts, etc.
             return response()->json(['error' => 'API request failed: ' . $e->getMessage()], 500);
         }
 
     }
 

    
}
