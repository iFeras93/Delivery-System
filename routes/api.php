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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'Api\V1\AuthorizationController@login');
    Route::post('register', 'Api\V1\AuthorizationController@register');

    Route::resource('orders', 'Api\V1\OrdersController')->middleware('auth:api');
});