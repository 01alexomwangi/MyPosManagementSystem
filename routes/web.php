<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
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
    Route::get('/reports/daily-sales', 'ReportController@dailySales')->name('reports.daily-sales');
    Route::get('/reports/weekly-sales', 'ReportController@weeklySales')->name('reports.weekly-sales');
    Route::get('/reports/monthly-sales', 'ReportController@monthlySales')->name('reports.monthly-sales');

    // ----------------------------
    // POS Resources
    // ----------------------------
    Route::resource('/orders', 'OrderController');
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
    Route::resource('/suppliers','SupplierController');
    Route::resource('/users','UserController');
    Route::resource('/companies','CompanyController');
    Route::resource('/transactions','TransactionController');
    Route::resource('/brands', 'BrandController');
    Route::resource('/categories', 'CategoryController');
});
