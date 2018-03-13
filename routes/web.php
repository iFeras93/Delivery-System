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

    // Admin Dashboard Route
    Route::get('dashboard', 'AdminController@index')->name('admin.dashboard');


    // Management Users Routes
    Route::resource('users', 'Admin\UsersController', ['except' => ['create', 'edit', 'store', 'update', 'destroy']]);
    Route::post('users/{id}/update', 'Admin\UsersController@update')->name('users.update');
    Route::post('users/{id}/destroy', 'Admin\UsersController@destroy')->name('users.destroy');

    // Management Products Routes
    Route::resource('products', 'Admin\ProductsController', ['except' => ['edit', 'update', 'destroy']]);
    Route::post('products/{id}/update', 'Admin\ProductsController@update')->name('products.update');
    Route::post('products/{id}/destroy', 'Admin\ProductsController@destroy')->name('products.destroy');

    // Site Settings Routes
    Route::resource('settings', 'Admin\SettingsController', ['except' => ['create', 'show', 'edit', 'store', 'update', 'destroy']]);
    Route::post('settings/update', 'Admin\SettingsController@update')->name('settings.update');


    // Management Users Routes
    Route::resource('orders', 'Admin\OrdersController', ['except' => ['edit', 'update', 'destroy']]);
    Route::post('orders/{id}/update', 'Admin\OrdersController@update')->name('orders.update');
    Route::post('orders/{id}/destroy', 'Admin\OrdersController@destroy')->name('orders.destroy');


});