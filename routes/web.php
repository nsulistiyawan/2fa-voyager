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


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('verify-2fa', ['uses' => 'Login2FaController@verify2fa', 'as' => 'admin.verify-2fa']);
    Route::post('verify-2fa', ['uses' => 'Login2FaController@doVerify2fa', 'as' => 'admin.do-verify-2fa']);

    Route::get('setup-2fa', ['uses' => 'Login2FaController@setup2fa', 'as' => 'admin.setup-2fa']);
    Route::post('setup-2fa', ['uses' => 'Login2FaController@doSetup2fa', 'as' => 'admin.do-setup-2fa']);

    Route::post('logout', ['uses' => 'Login2FaController@logout',  'as' => 'voyager.logout']);
});
