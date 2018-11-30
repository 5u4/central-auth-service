<?php

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

/* Version 1 */
Route::group(['prefix' => 'v1'], function () {
    /* Auth */
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/token', 'AuthController@refreshToken')->middleware('auth');
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
    });
});
