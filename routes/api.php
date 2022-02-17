<?php

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



Route::middleware(['jwt', 'role:admin'])->prefix('admin')->group(function () {
    Route::post('/create', 'Admin\AdminController@create');
    Route::get('/user-listing', 'Admin\AdminController@user_listing');
    Route::post('/login', 'Admin\AdminController@login')->withoutMiddleware(['jwt','role:admin']);
});

Route::middleware(['jwt', 'role:user'])->prefix('user')->group(function () {
    Route::post('/create', 'User\UserController@create')->withoutMiddleware(['jwt','role:user']);
    Route::put('/update', 'User\UserController@update');
    Route::delete('/delete', 'User\UserController@delete');
    // Route::get('/user-listing', 'Admin\AdminController@user_listing');
    Route::post('/login', 'User\UserController@login')->withoutMiddleware(['jwt','role:user']);
    Route::post('/forgot-password', 'User\UserController@forgot_password')->withoutMiddleware(['jwt','role:user']);
    Route::post('/reset-password-token', 'User\UserController@reset_password_token')->withoutMiddleware(['jwt','role:user']);
    Route::get('/orders', 'User\UserController@orders');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
