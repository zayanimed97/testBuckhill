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
    Route::post('/login', 'Admin\AdminController@login')->withoutMiddleware(['jwt','role']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
