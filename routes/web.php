<?php

use App\User; 

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





Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', 'AuthController@showLogin')->name('login');//listens for GET requests to the /login URL.
Route::post('/login', [AuthController::class, 'login']);  //listens for POST requests to /login

Route::get('/register', 'AuthController@showRegister');
Route::post('/register', [AuthController::class, 'register']);

// Routes that require login
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home');
    });


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

     Route::get(
        '/reports/daily-sales',
        'ReportController@dailySales'
    )->name('reports.daily-sales');
    // Protected resources
    Route::resource('/orders','OrderController');
    Route::resource('/products','ProductController');
    Route::resource('/locations', 'LocationController');
    Route::resource('/sales', 'SaleController');
   
  });

    Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('/suppliers','SupplierController');
    Route::resource('/users','UserController');
    Route::resource('/companies','CompanyController');
    Route::resource('/transactions','TransactionController');
    Route::resource('/brands', 'BrandController');
    Route::resource('/categories', 'CategoryController');
    // Route::resource('/locations', 'LocationController');
    // Route::resource('/orders','OrderController');
    // Route::resource('/products','ProductController');

  });
