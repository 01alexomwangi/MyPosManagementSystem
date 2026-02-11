<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CustomerCartController;


Route::get('/', 'StoreController@index')->name('store.index');
Route::get('/product/{id}', 'StoreController@show')->name('store.show');

Route::get('/customer/register', 'CustomerAuthController@registerForm');
Route::post('/customer/register', 'CustomerAuthController@register')->name('customer.register');

Route::get('/customer/login', 'CustomerAuthController@loginForm');
Route::post('/customer/login', 'CustomerAuthController@login');
Route::post('/customer/logout', 'CustomerAuthController@logout');

Route::post('/cart/add/{id}', 'CustomerCartController@add')->name('cart.add');
Route::post('/cart/update/{id}', 'CustomerCartController@updateQuantity')->name('cart.update');
Route::post('/cart/checkout', 'CustomerCartController@checkout')->name('customer.cart.checkout');
Route::post('/cart/clear', 'CustomerCartController@clearCart')->name('cart.clear');
Route::get('/cart', 'CustomerCartController@cart')->name('cart.view');
Route::post('/cart/remove/{id}', 'CustomerCartController@remove')->name('cart.remove');




Route::get('/cashier/pending', 'CashierController@pendingSales')->name('cashier.pending');
Route::post('/cashier/complete/{id}', 'CashierController@completeSale')->name('cashier.complete');




Route::get('/pos', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', 'AuthController@showLogin')->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', 'AuthController@showRegister');
Route::post('/register', [AuthController::class, 'register']);

// Routes that require login
Route::middleware('auth')->group(function () {

    Route::get('/home', function () {
        return view('home');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ----------------------------
    // REPORTS
    // ----------------------------
    // Daily sales report
Route::get('/reports/daily', 'ReportController@dailySales')
    ->name('reports.daily');

// Weekly sales report
Route::get('/reports/weekly', 'ReportController@weeklySales')
    ->name('reports.weekly');

// Monthly sales report
Route::get('/reports/monthly', 'ReportController@monthlySales')
    ->name('reports.monthly');

// Custom date range report (from/to form)
Route::get('/reports/custom', 'ReportController@customReport')
    ->name('reports.custom');

     Route::get('/reports/receipts', 'ReportController@allReceipts')
     ->name('reports.receipts');

     // Export
Route::get('/reports/export/pdf', 'ReportController@exportPdf')->name('reports.export.pdf');
Route::get('/reports/export/excel', 'ReportController@exportExcel')->name('reports.export.excel');



    // ----------------------------
    // POS Resources
    // ----------------------------
  
    Route::resource('/products', 'ProductController');
    Route::resource('/locations', 'LocationController');
    Route::resource('/sales', 'SaleController');

    // ----------------------------
    // Manager-Specific Routes
    // Only managers can see branch users
    // ----------------------------
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

// ----------------------------
// Admin Routes
// ----------------------------
Route::middleware(['auth','admin'])->group(function () {
    Route::resource('/users','UserController');
    Route::resource('/brands', 'BrandController');
    Route::resource('/categories', 'CategoryController');
});
