<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\User;  // <-- NOT App\Models\User
use App\Http\Controllers\CompanyController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('/orders','OrderController');// orders.index
Route::resource('/products','ProductController');// products.index
Route::resource('/suppliers','SupplierController');// suppliers.index
Route::resource('/users','UserController');// users.index
Route::resource('/companies','CompanyController');// companies.index
Route::resource('/transactions','TransactionController');// transactions.index
Route::resource('/brands', 'BrandController');
Route::resource('/categories', 'CategoryController');



// Route::get('/kick-user/{id}', function ($id) {
//     $user = User::findOrFail($id);

//     if ($user->session_id) {
//         // Destroy the user's session
//         Session::getHandler()->destroy($user->session_id);

//         // Optional: clear session_id field
//         $user->session_id = null;
//         $user->save();
//     }

//     return "User {$user->name} has been kicked out!";
// });