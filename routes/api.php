<?php

use Illuminate\Support\Facades\Route;

    // ✅ Public
    Route::post('/login', 'Api\AuthController@login');
    Route::get('/store/products',          'Api\StoreController@products');
    Route::get('/store/products/{id}',     'Api\StoreController@product');
    Route::get('/store/locations',         'Api\StoreController@locations');
    Route::post('/store/register',         'Api\StoreController@register');
    Route::post('/store/login',            'Api\StoreController@login');
    // ✅ Authenticated users
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/store/logout',            'Api\StoreController@logout');
    Route::post('/store/checkout',          'Api\StoreController@checkout');
    Route::post('/store/estimate-delivery', 'Api\StoreController@estimateDelivery');
    Route::get('/store/orders',             'Api\StoreController@orders');
    Route::get('/store/orders/{id}',        'Api\StoreController@order');


    Route::post('/logout',         'Api\AuthController@logout');
    Route::get('/profile',         'Api\AuthController@profile');
    Route::put('/profile',         'Api\AuthController@updateProfile');
    Route::put('/change-password', 'Api\AuthController@changePassword');

     
    // ✅ Everyone (admin, manager, cashier)
    Route::get('/orders',              'Api\OrderController@index');
    Route::get('/orders/{id}',         'Api\OrderController@show');
    Route::post('/orders',             'Api\OrderController@store');
    Route::put('/orders/{id}/status', 'Api\OrderController@updateStatus');

    // ✅ Everyone (admin, manager, cashier)
    Route::get('/locations', 'Api\LocationController@index');

    // ✅ Reports — all authenticated users
    Route::get('/reports/summary', 'Api\ReportController@summary');
    Route::get('/reports/daily',   'Api\ReportController@daily');
    Route::get('/reports/weekly',  'Api\ReportController@weekly');
    Route::get('/reports/monthly', 'Api\ReportController@monthly');
    Route::get('/reports/custom',  'Api\ReportController@custom');

    // ✅ Everyone (admin, manager, cashier)
    Route::get('/products',        'Api\ProductController@index');
    Route::get('/products/{id}',   'Api\ProductController@show');



    // ✅ Admin and Manager only
    Route::middleware('isAdminOrManager')->group(function () {
        

        Route::post('/products',        'Api\ProductController@store');
        Route::put('/products/{id}',    'Api\ProductController@update');
        Route::delete('/products/{id}',   'Api\ProductController@destroy');
    });

    // ✅ Admin only
    Route::middleware('isAdmin')->group(function () {

        // Users
        Route::post('/payments/{payment}/verify', 'Api\PaymentController@verify');
        Route::get('/users',                     'Api\UserController@index');
        Route::get('/users/{id}',                'Api\UserController@show');
        Route::post('/users',                    'Api\UserController@store');
        Route::put('/users/{id}',                'Api\UserController@update');
        Route::delete('/users/{id}',             'Api\UserController@destroy');
        Route::put('/users/{id}/reset-password', 'Api\UserController@resetPassword');
    });
});