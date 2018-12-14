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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
////    return $request->user();
////});

// test endpoint
Route::get('/property', 'PropertyController@index');

Route::post('/availability/search', 'AvailabilityController@search');
Route::post('/availability/enquire-now', 'AvailabilityController@enquire');