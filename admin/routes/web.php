<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');
Auth::routes();

Route::get('/logout', function(){
    Auth::logout();
    return Redirect::to('/');
 });

 Route::get('clear', function () {
    $output =    Artisan::call('schedule:run'); // Replace 'your:command' with the actual command name
    //sleep(5);
    return  $output;
});

 Route::get('/about', 'HomeController@about');
 Route::get('/faq', 'HomeController@faq');

