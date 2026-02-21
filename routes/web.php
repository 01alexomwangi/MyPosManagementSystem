<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| STORE (ECOMMERCE FRONT)
|--------------------------------------------------------------------------
*/

Route::get('/', 'StoreController@index')->name('store.index');
Route::get('/product/{id}', 'StoreController@show')->name('store.show');

Route::post('/set-location', 'StoreController@setLocation')->name('store.setLocation');

/*
|--------------------------------------------------------------------------
| CUSTOMER AUTH
|--------------------------------------------------------------------------
*/

Route::get('/customer/register', 'CustomerAuthController@registerForm');
Route::post('/customer/register', 'CustomerAuthController@register')->name('customer.register');

Route::get('/customer/login', 'CustomerAuthController@loginForm');
Route::post('/customer/login', 'CustomerAuthController@login');
Route::post('/customer/logout', 'CustomerAuthController@logout');

/*
|--------------------------------------------------------------------------
| CUSTOMER CART
|--------------------------------------------------------------------------
*/

Route::get('/cart', 'CustomerCartController@cart')->name('cart.view');
Route::post('/cart/add/{id}', 'CustomerCartController@add')->name('cart.add');
Route::post('/cart/update/{id}', 'CustomerCartController@updateQuantity')->name('cart.update');
Route::post('/cart/remove/{id}', 'CustomerCartController@remove')->name('cart.remove');
Route::post('/cart/clear', 'CustomerCartController@clearCart')->name('cart.clear');
Route::post('/cart/checkout', 'CustomerCartController@checkout')->name('customer.cart.checkout');


Route::post('/orders', 'OrderController@store')->name('orders.store');
Route::get('/orders/{id}', 'OrderController@show')->name('orders.show');

Route::get('/payments/{order}/initiate', 'PaymentController@initiate')
        ->name('payments.initiate');

Route::post('/payment/webhook', 'PaymentController@webhook');

Route::get('/store/orders', 'CustomerOrderController@index')
    ->name('store.orders');

    Route::get('/store/order/{id}/success', 'CustomerOrderController@success')
    ->name('store.order.success');


    Route::post('/orders/{order}/update-status', 'OrderController@updateStatus')
    ->name('orders.updateStatus');

   

    









/*
|--------------------------------------------------------------------------
| AUTH (STAFF LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/pos', function () {
    return view('welcome');
});

Route::get('/login', 'AuthController@showLogin')->name('login');
Route::post('/login', 'AuthController@login');
Route::get('/register', 'AuthController@showRegister');
Route::post('/register', 'AuthController@register');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', 'AuthController@logout')->name('logout');

    Route::get('/home', function () {
        return view('home');
    });

    /*
    |--------------------------------------------------------------------------
    | REPORTS (Now Based on Orders)
    |--------------------------------------------------------------------------
    */

   Route::get('/reports/daily', 'ReportController@dailyOrders')
    ->name('reports.daily');

Route::get('/reports/weekly', 'ReportController@weeklyOrders')
    ->name('reports.weekly');

Route::get('/reports/monthly', 'ReportController@monthlyOrders')
    ->name('reports.monthly');

Route::get('/reports/custom', 'ReportController@customReport')
    ->name('reports.custom');

Route::get('/reports/receipts', 'ReportController@allReceipts')
    ->name('reports.receipts');


    Route::get('/reports/export/pdf', 'ReportController@exportPdf')->name('reports.export.pdf');
    Route::get('/reports/export/excel', 'ReportController@exportExcel')->name('reports.export.excel');

    /*
    |--------------------------------------------------------------------------
    | POS (ORDER SYSTEM — REPLACED SALES)
    |--------------------------------------------------------------------------
    */

    Route::resource('/orders', 'OrderController');

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS & LOCATIONS
    |--------------------------------------------------------------------------
    */

    Route::resource('/products', 'ProductController');
    Route::resource('/locations', 'LocationController');

    /*
    |--------------------------------------------------------------------------
    | MANAGER ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware(['manager'])->group(function () {
        Route::get('/branch/users', function() {
            $manager = auth()->user();

            $users = \App\User::where('location_id', $manager->location_id)
                ->where('role', 'cashier')
                ->get();

            return view('users.branch', compact('users'));
        })->name('branch.users');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','admin'])->group(function () {

    Route::resource('/users','UserController');
    Route::resource('/brands', 'BrandController');
    Route::resource('/categories', 'CategoryController');
    Route::get('/logs', 'SystemLogController@logs')
    ->name('admin.logs');

    Route::post('/payments/{payment}/verify', 'PaymentController@verify')
    ->name('payments.verify');


});
