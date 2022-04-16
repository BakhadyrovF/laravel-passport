<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::middleware('guest:api')->group(function () {
        Route::post('/sign-up', 'AuthController@signUp')->name('sign-up');
        Route::post('/sign-in', 'AuthController@signIn')->name('sign-in');
    });

    // Protected routes
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/log-out', 'AuthController@logout')->name('log-out');
        Route::get('/user-data', 'AuthController@userData')->name('user-data');
    });
});
