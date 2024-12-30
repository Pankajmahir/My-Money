<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Models\UserActivity;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {


    // return $request->user();
    // for message and call less than 0 then it show value 0 condition.
    $user =  $request->user();

    $today = now()->format('Y-m-d');
    $userActivity = UserActivity::where('user_id', $user->id)
                                ->where('activity_date', $today)
                                ->first();

    if (!$userActivity) {
        UserActivity::create([
            'user_id' => $user->id,
            'activity_date' => $today,
        ]);
    }
    $user->alert_limit = 5;
    if ($user->total_call < 0 ) {
        $user->total_call = 0;
    }
    if($user->total_message < 0){
        $user->total_message = 0;
        return $request->user();
    }
    return $request->user();
});

Route::get('is_app_on_maintenance', 'Api\AuthController@CheckMaintenanceMode');
Route::post('login', 'Api\AuthController@SendOtp'); // SendOTP
Route::post('otpvarify', 'Api\AuthController@VarifyOTP');  //OTPVarify
Route::post('general_settings', 'Api\UserController@genralSetting');
Route::post('notification', 'Api\NotificationController@getUserData');   //get user Notifiacation
Route::post('sendreminder ', 'Api\NotificationController@SendReminder'); //sendreminder

Route::group(['middleware' => ['auth:sanctum']], function(){

    //Logout
    Route::post('logout', 'Api\AuthController@logout'); //Logout

    //Dashborad Api
    Route::post('dashboard','Api\UserController@getUserDashboard');  //Dashbaord Api

    //User API
    Route::post('get-user-by-access_token', 'Api\UserController@getUserInfoByAccessToken');  //User acces by token
    Route::post('users/update', 'Api\UserController@userUpdate');  // User Update
    Route::get('delete-account', 'Api\UserController@delete_account'); //delete account

    //Business Api
    Route::post('business', 'Api\BusinessController@GetBusiness'); //User All Business
    Route::post('business/create', 'Api\BusinessController@BusinessCreate'); //Create bussiness
    Route::post('business/update', 'Api\BusinessController@BusinessUpdate'); //Update bussiness
    Route::post('business/destroy', 'Api\BusinessController@BusinessDelete'); //Delete bussiness
    Route::post('default/business', 'Api\BusinessController@DefaultBusiness');  //Default Business

    //Customer Api
    Route::post('customers', 'Api\CustomerController@index');  //All Customer
    Route::post('customers/create', 'Api\CustomerController@create');  //Create Customer
    Route::post('customers/update', 'Api\CustomerController@update');  //Update Customer
    Route::post('customers/destroy', 'Api\CustomerController@destory');  //Delete Customer
    Route::post('customers/search', 'Api\CustomerController@CustomerSearch');  //Customer Search

    //Transection Sheet
    Route::post('transectionsheet', 'Api\TransectionSheetController@create');   //Transection Details
    Route::post('transectionsheet/update', 'Api\TransectionSheetController@update');   //Transection Update
    Route::post('transectionsheet/destroy', 'Api\TransectionSheetController@destroy');   //Transection delete
    Route::post('business/transection', 'Api\TransectionSheetController@BusinessTransection'); // Bussiness Transection
    Route::post('transection/filter', 'Api\TransectionSheetController@TransectionFilter');

    //Account Api
    Route::post('bank_accounts', 'Api\BankAccountController@GetBankAccouniDetails');   //get Bank Account details
    Route::post('bank_accounts/update', 'Api\BankAccountController@updateBankAccount');  //bank account update

    //Notification

    Route::post('notification/destroy', 'Api\NotificationController@destroy');  //notification delete
    Route::post('getnotifications', 'Api\NotificationController@getNotification'); //get notification

    Route::post('notification/clears', 'Api\NotificationController@clear_notification'); //cleare notification
    Route::post('send-email', 'Api\NotificationController@send_email_notify'); //send email notification

    //Reminder
    Route::post('setreminder', 'Api\ReminderController@create');   //Remider create
    Route::post('setreminder/destroy', 'Api\ReminderController@destroy');   //Reminder destroy
    Route::post('setreminder/getreminder', 'Api\ReminderController@GetReminder');  //set Reminder
    Route::post('setreminder/smsorcalltext', 'Api\ReminderController@SMSorCALLText');  //set SMSorCALLText


    //Packages
    Route::get('getpackages', 'Api\PackageController@GetPackage');  // GetPackageName

    //RozerPay
    Route::post('rozerpay/order', 'Api\RozerPayController@createOrder');   //Order create
    Route::post('rozerpay/payment/create', 'Api\RozerPayController@createPayment');    //Payment create
    Route::post('rozerpay/payment/history', 'Api\RozerPayController@PaymentHistory');           //Get Tarnsection

    //refferal
    Route::post('ref_check', 'Api\UserController@ref_check');
    Route::post('ref_by', 'Api\UserController@ref_by');


    //phone pe routes by rahul
    Route::post('phonepay-callbck', 'Api\PhonePeController@handleCallback');
    Route::get('check-status', 'Api\PhonePeController@checkStatus');
    Route::post('create-trasactions', 'Api\PhonePeController@create_transactions');

    //store contact data
    Route::post('store-contact', 'Api\ContactController@store');

});


    Route::post('device/token',  'Api\UserController@DeviceToken');  //DeviceTOken
    Route::get('callbackurl', 'Api\NotificationController@callbackurl'); //call back url for callig status
    Route::get('sendsamadummy', 'Api\UserController@sendsamadummy'); //dummy sms
    Route::get('reminder_cron', 'Api\ReminderController@reminder_cron'); //reminder cron job
    Route::get('check-payment-status', 'Api\PhonePeController@checkPaymentStatus');// check payment status cron api

Route::fallback(function() {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});
Route::post('/versionCheck', 'VersionController@compareData');
Route::post('/getCallDetails','Api\CustomerController@getCallDetails');


