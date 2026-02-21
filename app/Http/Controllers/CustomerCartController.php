<?php

namespace App\Http\Controllers;


use App\Order;
use App\OrderItem;
use App\Payment;
use Illuminate\Support\Facades\DB;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerCartController extends Controller
{
   public function checkout(Request $request)
{
    if (!Session::has('customer_id')) {
        return redirect('/customer/login')
            ->with('error', 'Please login first.');
    }

     $request->validate([
        'delivery_method' => 'required|in:pickup,rider'
    ]);

    $cart = Session::get('cart', []);

    if (count($cart) === 0) {
        return back()->with('error', 'Your cart is empty.');
    }

    $selectedLocation = session('selected_location');

    if (!$selectedLocation) {
        return back()->with('error', 'Please select a location first.');
    }

    $customerId = Session::get('customer_id');

    try {

        $order = DB::transaction(function () use ($cart, $customerId, $selectedLocation, $request) {

            $total = 0;

            foreach ($cart as $item) {
                $total += $item['total_amount'];
            }


               // 🚚 Delivery Logic
            $deliveryFee = 0;

            if ($request->delivery_method === 'rider') {
                $deliveryFee = 1; // You can improve this later
            }

            $finalTotal = $total + $deliveryFee;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => null, // Online order
                'customer_id' => $customerId,
                'location_id' => $selectedLocation,
                'source' => 'online',
                 'total' => $finalTotal,
                'delivery_method' => $request->delivery_method,
                'delivery_fee' => $deliveryFee,
                'delivery_status' => 'pending',
                'status' => 'pending_payment'
            ]);

            foreach ($cart as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'amount' => $item['total_amount'],
                    'discount' => 0,
                ]);
            }

            return $order;
        });

        Session::forget('cart');

        return redirect()->route('payments.initiate', $order->id);

    } catch (\Exception $e) {
        return back()->with('error', 'Checkout failed. Please try again.');
    }
}

public function selectLocation(Request $request)
{
    session(['selected_location' => $request->location_id]);

    // Clear cart when location changes
    Session::forget('cart');

    return back()->with('success', 'Location selected successfully.');
}



public function cart()
{
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

 public function add(Request $request, $id)
{
    $cart = session()->get('cart', []);

    $product = Product::findOrFail($id);
    $quantity = (int) $request->quantity;

    // 🔐 1️⃣ Get selected location from session
    $selectedLocation = session('selected_location');

    // ❌ 2️⃣ If no location selected
    if (!$selectedLocation) {
        return response()->json([
            'success' => false,
            'message' => 'Please select a location first.'
        ]);
    }

    // ❌ 3️⃣ If product is not in selected location
    if ($product->location_id != $selectedLocation) {
        return response()->json([
            'success' => false,
            'message' => 'This product is not available in selected location.'
        ]);
    }

    // ✅ 4️⃣ If everything is valid, continue adding to cart
    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $quantity;
    } else {
        $cart[$id] = [
            'product_id' => $product->id,
            'name' => $product->product_name,
            'price' => $product->price,
            'quantity' => $quantity,
        ];
    }

    $cart[$id]['total_amount'] =
        $cart[$id]['price'] * $cart[$id]['quantity'];

    session()->put('cart', $cart);

    $totalQty = 0;
    $totalAmount = 0;

    foreach ($cart as $item) {
        $totalQty += $item['quantity'];
        $totalAmount += $item['total_amount'];
    }

    return response()->json([
        'success' => true,
        'cartCount' => $totalQty,
        'cartTotal' => $totalAmount
    ]);
}



public function updateQuantity(Request $request, $productId)
{
    $cart = session()->get('cart', []);

    if(isset($cart[$productId])) {
        $cart[$productId]['quantity'] = (int)$request->quantity;
        $cart[$productId]['total_amount'] =
            $cart[$productId]['price'] * $request->quantity;

        session()->put('cart', $cart);
    }

    $totalQty = 0;
    $totalAmount = 0;

    foreach($cart as $item){
        $totalQty += $item['quantity'];
        $totalAmount += $item['total_amount'];
    }

    return response()->json([
        'success' => true,
        'cartCount' => $totalQty,
        'cartTotal' => $totalAmount
    ]);
}


      // Remove product
    public function remove($id)
    {
        $cart = Session::get('cart', []);

        if(isset($cart[$id])){
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        $totalQty = 0;
        $totalAmount = 0;
        foreach($cart as $item){
            $totalQty += $item['quantity'];
            $totalAmount += $item['total_amount'];
        }

        return response()->json([
            'success' => true,
            'cartCount' => $totalQty,
            'cartTotal' => $totalAmount
        ]);
    }



}

