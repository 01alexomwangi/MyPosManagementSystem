<?php

use Illuminate\Support\Facades\Route;

// ✅ Public
Route::post('/login', 'Api\AuthController@login');

// ✅ Authenticated users
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout',         'Api\AuthController@logout');
    Route::get('/profile',         'Api\AuthController@profile');
    Route::put('/profile',         'Api\AuthController@updateProfile');
    Route::put('/change-password', 'Api\AuthController@changePassword');

     
    // ✅ Everyone (admin, manager, cashier)
    Route::get('/orders',              'Api\OrderController@index');
    Route::get('/orders/{id}',         'Api\OrderController@show');
    Route::post('/orders',             'Api\OrderController@store');

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
        Route::put('/orders/{id}/status', 'Api\OrderController@updateStatus');

        Route::post('/products',        'Api\ProductController@store');
        Route::put('/products/{id}',    'Api\ProductController@update');
        Route::delete('/products/{id}',   'Api\ProductController@destroy');
    });

    // ✅ Admin only
    Route::middleware('isAdmin')->group(function () {

        // Users
        Route::get('/users',                     'Api\UserController@index');
        Route::get('/users/{id}',                'Api\UserController@show');
        Route::post('/users',                    'Api\UserController@store');
        Route::put('/users/{id}',                'Api\UserController@update');
        Route::delete('/users/{id}',             'Api\UserController@destroy');
        Route::put('/users/{id}/reset-password', 'Api\UserController@resetPassword');
    });
});