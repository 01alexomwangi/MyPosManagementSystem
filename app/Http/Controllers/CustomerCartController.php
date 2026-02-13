<?php

namespace App\Http\Controllers;


use App\PendingSale;
use App\PendingSaleItem;
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

    $cart = Session::get('cart', []);

    if (count($cart) === 0) {
        return redirect()->back()
            ->with('error', 'Your cart is empty.');
    }

    $customerId = Session::get('customer_id');

    // STEP 1: Group cart items by location
    $groupByLocation = [];

    foreach ($cart as $item) {

        $product = Product::find($item['product_id']);
        if (!$product) continue;

        $locationId = $product->location_id;

        if (!isset($groupByLocation[$locationId])) {
            $groupByLocation[$locationId] = [];
        }

        $groupByLocation[$locationId][] = $item;
    }

    // STEP 2: Create pending sale per location
    foreach ($groupByLocation as $locationId => $items) {

        $locationTotal = 0;

        foreach ($items as $item) {
            $locationTotal += $item['total_amount'];
        }

        $pendingSale = PendingSale::create([
            'customer_id' => $customerId,
            'location_id' => $locationId,
            'total'       => $locationTotal,
            'status'      => 'pending',
        ]);

        foreach ($items as $item) {
            PendingSaleItem::create([
                'pending_sale_id' => $pendingSale->id,
                'product_id'      => $item['product_id'],
                'quantity'        => $item['quantity'],
                'price'           => $item['price'],
                'total_amount'    => $item['total_amount'],
            ]);
        }
    }

    Session::forget('cart');

    return redirect('/')
        ->with('success', 'Order sent to cashier for processing!');
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

