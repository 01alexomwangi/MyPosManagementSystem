<?php

namespace App\Http\Controllers;
use App\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{

     // Show registration form
    public function registerForm()
    {
        return view('customer.register');
    }

    public function register(Request $request)
   {
    $customer = Customer::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Session::put('customer_id', $customer->id);
    return redirect('/');
   }
     

     // Show login form
    public function loginForm()
    {
        return view('customer.login');
    }
    
   public function login(Request $request)
   {
    $customer = Customer::where('email', $request->email)->first();

    if ($customer && Hash::check($request->password, $customer->password)) {
        Session::put('customer_id', $customer->id);
        return redirect('/');
    }

    return back()->with('error', 'Invalid login');
    }

    public function logout()
     {
    Session::forget('customer_id');
    return redirect('/');
     }




}
