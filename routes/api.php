<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'App\\Http\\Controllers\\Api\\Admin'],function() {

    Route::apiResource('users','UserController');
    Route::delete('users','UserController@bulkDelete');
    Route::patch('users/{user}/change-role','UserController@changeRole');

    Route::get('appointments','AppointmentController@index');
    Route::post('appointments/create','AppointmentController@store');
    Route::get('appointments/{appointment}/edit','AppointmentController@edit');
    Route::put('appointments/{appointment}/edit','AppointmentController@update');
    Route::get('appointments/status','AppointmentController@getStatusWithCount');

    Route::get('clients','ClientController@index');
});
