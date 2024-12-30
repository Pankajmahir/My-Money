<?php

use App\Exports\CallSmsExport;
use App\Exports\MoneyGotExport;
use App\Exports\UserActivityExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

Route::get('/admin', 'HomeController@adminDashboard')->name('admin.dashboard')->middleware(['auth', 'admin']);
Route::group(['prefix' =>'admin', 'middleware' => ['admin']], function(){
    Route::get('/dashboard', 'HomeController@adminDashboard')->name('home');
    Route::post('changepassword', 'HomeController@admin_passwordChange')->name('admin.changePassword');

    //User Details
    Route::resource('users', 'UserController');
    Route::post('users/destroy', 'UserController@destroy')->name('users.destroy');
    Route::post('/users/status', 'UserController@updateStatus')->name('users.status');
    Route::post('user/excel/download', 'UserController@ExcelDownload')->name('user.excel.download');

    //Package Details
    Route::resource('packages', 'PackageController');
    Route::post('packages/destroy', 'PackageController@destroy')->name('packages.destroy');
    Route::post('/packages/status', 'PackageController@updateStatus')->name('packages.status');

    //Membership Details
    Route::resource('membership', 'MembershipController');
    Route::post('membership/destroy', 'MembershipController@destroy')->name('membership.destroy');
    Route::post('/membership/status', 'MembershipController@updateStatus')->name('membership.status');

    //Transection Details
    Route::resource('transections', 'TransectionController');
    Route::post('transections/destroy', 'TransectionController@destroy')->name('transections.destroy');
    Route::post('/transections/status', 'TransectionController@updateStatus')->name('transections.status');

    // //Faq Details
    Route::resource('faqs', 'FaqController');
    Route::post('faqs/destroy', 'FaqController@destroy')->name('faqs.destroy');
    Route::post('/faqs/status', 'FaqController@updateStatus')->name('faqs.status');

    // //Abusives Details
    Route::resource('abusives', 'AbusiveController');
    Route::post('abusives/destroy', 'AbusiveController@destroy')->name('abusives.destroy');
    Route::post('/abusives/status', 'AbusiveController@updateStatus')->name('abusives.status');

    // //Notification Details
    Route::resource('notifications', 'NotificationController');
    Route::post('notifications/destroy', 'NotificationController@destroy')->name('notifications.destroy');
    Route::post('/notifications/status', 'NotificationController@updateStatus')->name('notifications.status');

    //Report Details
    Route::get('report/user', 'ReportController@userReport')->name('user.report');
    Route::post('users/data', 'ReportController@userData')->name('users.month.data');
    Route::get('transection/user', 'ReportController@transectionReport')->name('transection.report');
    Route::post('transection/user', 'ReportController@SingleUser')->name('transection.single.user');
    Route::post('transection/month', 'ReportController@MonthwiseData')->name('transection.month.data');

    Route::get('money-got-report', 'ReportController@moneyGotReport')->name('money-got-report');
    Route::get('/export-money-got-report', function () {
        $startDate = request()->query('start_date');
        $endDate = request()->query('end_date');
        $userId = request()->query('user_id');
        $type = request()->query('type') ?? 'GOT';
        $fileName = 'money_'.strtolower($type).'_report.csv';

        return Excel::download(new MoneyGotExport($startDate, $endDate, $userId,$type), $fileName);
    })->name('money-got-export');

    Route::get('/call-sms-report','ReportController@callSmsReport')->name('call-sms-report');
    Route::get('/export-call-sms-report', function () {
        $startDate = request()->query('start_date');
        $endDate = request()->query('end_date');
        $userId = request()->query('user_id');
        $type = request()->query('type');

        return Excel::download(new CallSmsExport($startDate, $endDate, $userId, $type), 'call_sms_report.csv');
    })->name('export-call-sms-report');

    Route::get('/device-user-report', 'ReportController@deviceUserReport')->name('device-user-report');

    Route::get('/user-activity-report', 'ReportController@userActivityReport')->name('user-activity-report');
    Route::get('/user-activity-export', function (Request $request) {
        return Excel::download(new UserActivityExport($request), 'daily_active_users_report.csv');
    })->name('user-activity-export');

    Route::get('/monthly-user-activity-report', 'ReportController@monthlyUserActivityReport')->name('monthly-user-activity-report');
    Route::get('/user-activity-export-csv','ReportController@exportMonthlyReportCsv')->name('user-activity-export-csv');

    Route::get('/export-combined-user-report','ReportController@exportCombinedUserReport')->name('export-combined-user-report');

    //general Setting
    Route::resource('general_settings', 'GeneralSettingController');

    //Send Notification
    Route::resource('send-notifications', 'SendNotificationController');
    Route::post('send-notifications/destroy', 'SendNotificationController@destroy')->name('send-notifications.destroy');


 Route::get('/version', 'VersionController@index')->name('version.showData');
 Route::post('/version', 'VersionController@store')->name('version.storeData');
Route::get('/version-delete/{id}','VersionController@delete')->name('version.delete');
Route::get("/version-edit/{id}", 'VersionController@edit');
 Route::post("/version-edit", 'VersionController@update')->name('version.updateData');

 Route::post('/version/status', 'VersionController@statusUpdate')->name('version.status');
 Route::get('migrare', function () {
    Artisan::call('migrate:refresh');
});
});
