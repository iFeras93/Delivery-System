<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


/**
 * Admin Routs
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('dashboard', 'AdminController@index')->name('admin.dashboard');

    Route::resource('users', 'Admin\UsersController', ['except' => ['create', 'edit', 'store', 'update','destroy']]);
    Route::post('users/{id}/update', 'Admin\UsersController@update')->name('users.update');
    Route::post('users/{id}/destroy', 'Admin\UsersController@destroy')->name('users.destroy');

    Route::get('restaurants', 'AdminController@getRestList')->name('admin.restList');
});