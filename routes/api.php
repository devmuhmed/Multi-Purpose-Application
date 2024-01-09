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
    Route::get('users','UserController@index');
    Route::post('users','UserController@store');
    Route::patch('users/{user}/change-role','UserController@changeRole');
    Route::put('users/{user}','UserController@update');
    Route::delete('users/{user}','UserController@destroy');
    Route::delete('users','UserController@bulkDelete');
    Route::get('users/search','UserController@search');

    Route::get('appointments','AppointmentController@index');
    Route::post('appointments/create','AppointmentController@store');
    Route::get('appointments/status','AppointmentController@getStatusWithCount');
});
