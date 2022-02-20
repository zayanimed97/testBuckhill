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

Route::prefix('main')->group(function(){
    Route::get('promotions', 'MainPageController@promotions');
    Route::get('blog', 'MainPageController@blog');
    Route::get('blog/{uuid}', 'MainPageController@blogPost');
});

Route::middleware(['jwt', 'role:admin'])->prefix('category')->group(function () {
    Route::post('/create', 'Admin\CategoriesController@create');
    Route::put('/{uuid}', 'Admin\CategoriesController@update');
    Route::delete('/{uuid}', 'Admin\CategoriesController@delete');
    Route::get('/{uuid}', 'Admin\CategoriesController@getCategory')->withoutMiddleware(['jwt','role:admin']);
});
Route::get('/categories', 'Admin\CategoriesController@categories');

Route::middleware(['jwt', 'role:admin'])->prefix('brand')->group(function () {
    Route::post('/create', 'Admin\BrandsController@create');
    Route::put('/{uuid}', 'Admin\BrandsController@update');
    Route::delete('/{uuid}', 'Admin\BrandsController@delete');
    Route::get('/{uuid}', 'Admin\BrandsController@getBrand')->withoutMiddleware(['jwt','role:admin']);
});
Route::get('/brands', 'Admin\BrandsController@brands');

// Orders
Route::get('/order/{uuid}', 'OrdersController@getOrder')->middleware(['jwt','role:admin']);
Route::get('/orders', 'OrdersController@orders')->middleware(['jwt','role:admin']);
Route::post('/order/create', 'OrdersController@create')->middleware(['jwt','role:user']);
Route::put('/order/{uuid}', 'OrdersController@update')->middleware(['jwt','role:user']);
Route::get('/orders/dashboard', 'OrdersController@dashboard');
Route::get('/order/{uuid}/download', 'OrdersController@download');
Route::get('/orders/shipment-locator', 'OrdersController@shipmentLocator');
Route::delete('/order/delete/{uuid}', 'OrdersController@delete');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['jwt', 'role:admin'])->prefix('order-status')->group(function () {
    Route::post('/create', 'OrderStatusesController@create');
    Route::put('/{uuid}', 'OrderStatusesController@update');
    Route::delete('/{uuid}', 'OrderStatusesController@delete');
    Route::get('/{uuid}', 'OrderStatusesController@getOrderStatus')->withoutMiddleware(['jwt','role:admin']);
});
Route::get('/order-statuses', 'OrderStatusesController@orderStatuses');


Route::middleware(['jwt', 'role:admin'])->prefix('payment')->group(function () {
    Route::post('/create', 'PaymentsController@create');
    Route::put('/{uuid}', 'PaymentsController@update');
    Route::delete('/{uuid}', 'PaymentsController@delete');
    Route::get('/{uuid}', 'PaymentsController@getPayment')->withoutMiddleware(['jwt','role:admin']);
});
Route::get('/payments', 'PaymentsController@payments');