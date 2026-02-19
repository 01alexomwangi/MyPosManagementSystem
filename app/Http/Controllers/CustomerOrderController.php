<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function success($id)
    {
        $order = Order::with('items.product')
            ->where('customer_id', session('customer_id'))
            ->findOrFail($id);

        return view('store.order-success', compact('order'));
    }

    public function index()
{
    if (!session()->has('customer_id')) {
        return redirect('/customer/login');
    }

    $orders = Order::where('customer_id', session('customer_id'))
                ->latest()
                ->get();

    return view('store.orders', compact('orders'));
}

}
