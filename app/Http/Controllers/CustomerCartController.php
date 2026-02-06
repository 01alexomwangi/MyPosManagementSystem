<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PendingSale;
use App\Product;
use App\PendingSaleItem;
use Illuminate\Support\Facades\Session;

class CustomerCartController extends Controller
{
    public function checkout(Request $request)
{
    // 1️⃣ Customer must be logged in
    if (!Session::has('customer_id')) {
        return redirect('/customer/login')
            ->with('error', 'Please login first.');
    }

    // 2️⃣ Get cart from SESSION (NOT from request)
    $cart = Session::get('cart', []);

    if (count($cart) === 0) {
        return redirect()->back()
            ->with('error', 'Your cart is empty.');
    }

    $customerId = Session::get('customer_id');
    $total = 0;

    // 3️⃣ Calculate total safely
    foreach ($cart as $item) {
        $total += $item['total_amount'];
    }

   $firstProductId = $cart[array_key_first($cart)]['product_id'];
   $product = Product::find($firstProductId);

$pendingSale = PendingSale::create([
    'customer_id' => $customerId,
    'location_id' => $product->location_id, // <- ensures correct branch
    'total'       => $total,
    'status'      => 'pending',             // <- must be pending
]);

    // 5️⃣ Save each item
    foreach ($cart as $item) {
        PendingSaleItem::create([
            'pending_sale_id' => $pendingSale->id,
            'product_id'      => $item['product_id'],
            'quantity'        => $item['quantity'],
            'price'           => $item['price'],
            'total_amount'    => $item['total_amount'],
        ]);
    }

    // 6️⃣ Clear cart after checkout
    Session::forget('cart');

    // 7️⃣ Done
    return redirect('/')
        ->with('success', 'Order sent to cashier for processing!');
}


    // CustomerCartController
public function addToCart(Request $request, $productId)
{
    // Check if customer is logged in
    // if (!Session::has('customer_id')) {
    //     return redirect('/customer/login')->with('error', 'Please login first.');
    // }

    $cart = Session::get('cart', []); // get current cart or empty

    $product = Product::findOrFail($productId); // make sure App\Product is imported

    if(isset($cart[$productId])) {
        $cart[$productId]['quantity'] += 1;
        $cart[$productId]['total_amount'] = $cart[$productId]['quantity'] * $product->price;
    } else {
        $cart[$productId] = [
            'product_id' => $product->id,
            'name' => $product->product_name,
            'price' => $product->price,
            'quantity' => 1,
            'total_amount' => $product->price,
        ];
    }

    Session::put('cart', $cart); // save back to session

    return redirect()->back()->with('success', 'Added to cart!');
}



public function cart()
{
    if (!Session::has('customer_id')) {
        return redirect('/customer/login')->with('error', 'Please login first.');
    }

    $cart = Session::get('cart', []);
    return view('store.cart', compact('cart'));
}

public function clearCart()
{
    // Remove cart from session
    Session::forget('cart');

    return redirect()->back()
        ->with('success', 'Cart cleared successfully.');
}



}

