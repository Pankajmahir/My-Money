<?php
use App\Models\Reminderlogs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

//response api
if (! function_exists('ResponseAPI')) {
    function ResponseAPI($status=true,$message="successed data",$error=array(),$data=array(),$responsecode=200, $flag= "", $token="")
    {
        return response()->json(['success' => $status, 'message' => $message,'error'=>$error,'data'=>$data, 'flag'=>$flag, 'token'=>$token],$responsecode);
    }
}

if (! function_exists('areActiveRoutes')) {
    function areActiveRoutes(Array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }

    }
}

if (! function_exists('FinalPrice')) {
    function FinalPrice($price)
    {
        return '₹ '. $price;
    }
}

if (! function_exists('SendCall')) {
    function SendCall($to,$name,$amount,$sname=null)
    {
        if(strpos($to, '+91') !== false){
            $to = substr($to, 3);
        }
        $amount = str_replace('-', '', $amount);
        $name = str_replace(' ', '%20', $name);
        $sname = str_replace(' ', '%20', $sname);
        //test
        // $url ="http://obd.vccagent.com/OBDApi/PushData?ApiKey=bc1883b5b74f5d302913eb3a43f9c4ac&OBDTagId=b59f8a2b-210c-4ecf-b8ee-9064f66265d2&Number=".$to."&TTS=".$name.";".$amount;
        // english
        // $url ="http://obd.vccagent.com/OBDApi/PushData?ApiKey=bc1883b5b74f5d302913eb3a43f9c4ac&OBDTagId=b59f8a2b-210c-4ecf-b8ee-9064f66265d2&Number=".$to."&TTS=".$amount.";".$name;
        //hindi
        // $url ="http://obd.vccagent.com/OBDApi/PushData?ApiKey=bc1883b5b74f5d302913eb3a43f9c4ac&OBDTagId=441d9e6e-911a-4678-be58-b2c105f46c38&Number=".$to."&TTS=".$name."%20;%20".$amount;

        //old Api
       // $url ="http://obd.vccagent.com/OBDApi/PushData?ApiKey=bc1883b5b74f5d302913eb3a43f9c4ac&OBDTagId=59542ff0-00dd-4cfe-abfe-9ed8108c68fc&Number=".$to."&TTS=".$name.";".$sname.";Rs.".$amount;

        //new Api
        // $url = "http://obd.vccagent.com/OBDApi/PushData?ApiKey=bc1883b5b74f5d302913eb3a43f9c4ac&OBDTagId=b59f8a2b-210c-4ecf-b8ee-9064f66265d2&Number=".$to."&TTS=".$name.";".$sname.";Rs.".$amount;
        // $ch = curl_init();
        // curl_setopt($ch,CURLOPT_URL,$url);
        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        // $output=curl_exec($ch);
        // curl_close($ch);
        // Log::info('url: ' .$url.' , to:'. $to . ' , name: '. $name . ', sname: '.$sname. ', amount: '. $amount);

        // API endpoint
        $url = 'http://103.132.146.183/OBD_REST_API/api/OBD_Rest/SINGLE_CALLWithCustCall_ID';

        // Data to be sent in JSON format
        $data = [
            "UserName" => "TRAVINITIES",
            "Password" => "Travinities@123",
            "VoiceId" => "36306",
            "MSISDN" => $to,
            "CustCall_ID" => "173",
            "PARAM1" => $name,
            "PARAM2" => $sname,
            "PARAM3" => $amount,
            "PARAM4" => "",
            "OBD_TYPE" => "PRP_TRAVINITIES",
            "DTMF" => "",
            "THKS_VOX_ID" => ""
        ];

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,                             // Set API URL
            CURLOPT_RETURNTRANSFER => true,                  // Return response as string
            CURLOPT_CUSTOMREQUEST => "POST",                 // Set request method to POST
            CURLOPT_HTTPHEADER => [                          // Set headers
                'Content-Type: application/json',            // Content type header for JSON
            ],
            CURLOPT_POSTFIELDS => json_encode($data),        // Send the data as raw JSON
        ]);

        // Execute the request
        $response = curl_exec($curl);
        // dd($response);
        // Check for errors
        if (curl_errno($curl)) {
            return curl_error($curl);
        } else {
            // Output the response
           // Close cURL session
            curl_close($curl);
            return $response;

        }


    }
}

if (! function_exists('SendSMS')) {
    function SendSMS($to,$name,$sname,$amount)
    {

        $amount = str_replace('-', '', $amount);
        // $message="नमस्ते ".$name." आपका ".$sname." पर ".$amount." रुपये बकाया है कृपया जल्द से जल्द इसका भुगतान करे | ".$amount." यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद। टीम Travinities हमारी वेबसाइट पर जाएँ http://makemypayment.co.in/app.html";
        $message="नमस्ते ".$name." आपका ".$sname." पर ".$amount." रुपये बकाया है कृपया जल्द से जल्द इसका भुगतान करे | यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद। टीम MakeMyPayment हमारी वेबसाइट पर जाएँ onelink.to/wqajzy";
        $url ="http://sms.vccagent.com/ApiSmsHttp?UserId=TRAVNT@GMAIL.COM&pwd=pwd2022&Message=".urlencode($message)."&Contacts=".$to."&SenderId=MMPMNT&ServiceName=SMSTRANS&MessageType=2&DLTTemplateId=1707166599692348825";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output=curl_exec($ch);
        curl_errno($ch);
        curl_close($ch);
        return $output;
    }
}

//Send sms if call count less than 5
//send message call and sms less than 5;
if (! function_exists('SendNewCall')) {
    function SendNewCall($to,$message)
    {

        $url ="http://sms.vccagent.com/ApiSmsHttp?UserId=TRAVNT@GMAIL.COM&pwd=pwd2022&Message=".urlencode($message)."&Contacts=".$to."&SenderId=MMPMNT&ServiceName=SMSTRANS&MessageType=2&DLTTemplateId=1707170567569760889";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output=curl_exec($ch);
        print_r($output);
        curl_errno($ch);
        curl_close($ch);
        return $output;
    }
}

//send message sms less than 5;
if (! function_exists('SendNewSms')) {
    function SendNewSms($to,$message)
    {

        $url ="http://sms.vccagent.com/ApiSmsHttp?UserId=TRAVNT@GMAIL.COM&pwd=pwd2022&Message=".urlencode($message)."&Contacts=".$to."&SenderId=MMPMNT&ServiceName=SMSTRANS&MessageType=2&DLTTemplateId=1707170651118814697";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output=curl_exec($ch);
        print_r($output);
        curl_errno($ch);
        curl_close($ch);
        return $output;
    }
}

if (! function_exists('SendOTP')) {
    function SendOTP($to,$otp)
    {
        $url ="http://sms.vccagent.com/ApiSmsHttp?UserId=TRAVNT@GMAIL.COM&pwd=pwd2022&Message=Dear Customer , Your Repay OTP for login is ".$otp." Note: Please DO NOT SHARE this OTP with anyone. Team Travinities&Contacts=".$to."&SenderId=MMPMNT&ServiceName=SMSTRANS&MessageType=1&DLTTemplateId=1707170782099266656";
        $url = str_replace(" ", '%20', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $output=curl_exec($ch);
        curl_errno($ch);
        curl_close($ch);
        return $output;
    }
}



if (! function_exists('SendNotificationUser')) {
    // function SendNotificationUser($token,$message,$title)
    // {
    //         $url = 'https://fcm.googleapis.com/fcm/send';

    //         $fields = array (
    //                 'registration_ids' => array (
    //                     $token
    //                 ),
    //                 'data' => array (
    //                     'title' => $title,
    //                     'body' => $message,
    //                 ),
    //                 'notification'=>array(
    //                     'title'=>$title,
    //                     'body' => $message,
    //                 )
    //         );
    //         $fields = json_encode ( $fields );

    //         $headers = array (
    //                 'Authorization: key= ' . "AAAACBUmy8g:APA91bGlTz8LDCOrAvjA-as1ORoOtYb8RWkN_sQ1PlOYJ2O4S9uuYTMwsBO1IdqEu4edq59UttOyRRDoYWEf2VqF6RiOwY61mJvmGACojUu3RPvuW9BPS8HzJWUB8Bidj15SUqwTMoGF",
    //                 'Content-Type: application/json'
    //         );

    //         $ch = curl_init ();
    //         curl_setopt ( $ch, CURLOPT_URL, $url );
    //         curl_setopt ( $ch, CURLOPT_POST, true );
    //         curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    //         curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    //         curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

    //         $result = curl_exec ( $ch );
    //        //echo $result;
    //         curl_close ( $ch );
    // }

    function SendNotificationUser($tokens, $message, $title){
        // Path to the service account key file
        $serviceAccountPath = storage_path('app/public/make-my-payment-firebase-adminsdk-a1bvu-4d021677b6.json'); // Update with your actual path

        // Create a Google Client
        $client = new Google\Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Get the OAuth 2.0 token
        $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];

        // Prepare the API URL for FCM v1
        $projectId = 'make-my-payment'; // Replace with your Firebase project ID
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Prepare the payload with 'tokens' for multiple recipients
        $payload = [
            'message' => [
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                ],
                'data' => [
                    'title' => $title,
                    'body' => $message,
                ],
            ],
            'validate_only' => false, // You can set this to true to validate without sending
        ];

        $tokens = explode(',',$tokens);
        // Loop through tokens and send individually or as a batch
        foreach ($tokens as $token) {
            $payload['message']['token'] = $token;

            // Encode payload as JSON
            $fields = json_encode($payload);

            // Set headers
            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ];

            // Initialize CURL and send the request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

            $result = curl_exec($ch);
            $response = json_decode($result, true);


            // Check for errors
            // if (curl_errno($ch)) {
            //     \Log::error('FCM error: ' . curl_error($ch));
            // } else {
            //     if (isset($response['error'])) {
            //         if ($response['error']['details'][0]['errorCode'] === 'UNREGISTERED') {
            //             \Log::warning('Unregistered token detected: ' . $token);
            //         }
            //         \Log::error('FCM error: ' . $response['error']['message']);
            //     } else {
            //         \Log::info('FCM Response: ' . $result); // Log success
            //     }
            // }

            curl_close($ch);
        }
    }

    if (! function_exists('GenerateRandomString')) {
        function GenerateRandomString($length = 6) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }

    if (! function_exists('Counterreminder')) {
        function Counterreminder($id) {
          return Reminderlogs::where('reminder_id',$id)->get()->count() ?? 0;
        }
    }

}

    if (! function_exists('logNotification')) {
        function logNotification($userId, $customerId, $type)
        {
            $today = Carbon::today();

            // Check if a log already exists for today
            $existingLog = DB::table('notifications_log')
                ->where('user_id', $userId)
                ->where('customer_id', $customerId)
                ->where('type', $type)
                ->whereDate('created_at', $today)
                ->first();

            if ($existingLog) {
                // If log exists, increment the count
                DB::table('notifications_log')
                    ->where('id', $existingLog->id)
                    ->increment('count');
            } else {
                // If log doesn't exist, create a new entry
                DB::table('notifications_log')->insert([
                    'user_id' => $userId,
                    'customer_id' => $customerId,
                    'type' => $type,
                    'count' => 1, // Start with count 1
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    if (! function_exists('getTodayNotificationCount')) {
        function getTodayNotificationCount($userId, $customerId, $type)
        {
            $today = Carbon::today();

            // Sum the count for notifications sent today
            return DB::table('notifications_log')
                ->where('user_id', $userId)
                ->where('customer_id', $customerId)
                ->where('type', $type)
                ->whereDate('created_at', $today)
                ->sum('count');
            }
    }

    if (! function_exists('getSetting')) {
        function getSetting()
        {
            return DB::table('tbl_general_settings')->first();
        }
    }


    if (! function_exists('SendEmail')) {
        function SendEmail($to,$name,$sname,$amount)
        {

            $amount = str_replace('-', '', $amount);
            $message="नमस्ते ".$name." आपका ".$sname." पर ".$amount." रुपये बकाया है कृपया जल्द से जल्द इसका भुगतान करे | यदि आपने पहले ही बकाया राशि का भुगतान कर दिया है तो कृपया ध्यान न दें धन्यवाद। टीम MakeMyPayment हमारी वेबसाइट पर जाएँ www.makemypayment.co.in";

            $emailData  =
            [
                "email" => $to,
                "subject"=> "Payment Reminder",
                "content"=> $message
            ];


             Mail::send('email.email_notification', $emailData, function($message)  use($emailData){
                $message->to($emailData['email'], 'Makemypayment')
                // ->cc('makemypayment22@gmail.com')
                ->subject($emailData['subject']);
                $message->from('no-reply@makemypayment.co.in','Makemypayment');
             });

        }
    }

