<?php

use Illuminate\Http\Request;

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

Route::get('/', function(Request $request){
	return response()->json([
        'name' => config('app.name'),
        'token_type' => 'bearer',
        'version' => '1.0'
    ]);
});

Route::post('login', 'Api\AuthController@login');
Route::post('register', 'Api\AuthController@register');

Route::post('profile/photo', 'Api\UserController@photo');
Route::post('profile/update', 'Api\UserController@update');

Route::get('booking/buses', 'Api\BookingController@buses');
Route::get('booking/terminals', 'Api\BookingController@terminals');