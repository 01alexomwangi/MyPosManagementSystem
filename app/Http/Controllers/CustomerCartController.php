<?php

namespace App\Http\Controllers;


use App\Services\LittleApiService;
use App\Order;
use App\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Location;
use Illuminate\Support\Facades\Http;

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

                  $cart = Session::get('cart', []);
                  $subtotal = 0;
                  foreach ($cart as $item) {
                  $subtotal += $item['total_amount'];
                  }

                 
           // 🚚 Delivery Logic
          

                 $deliveryFee = 0;

                if ($request->delivery_method === 'rider') {
                    $deliveryFee = $request->delivery_fee ?? session('delivery_fee') ?? 0;

                    if ($deliveryFee <= 0) {
                        return back()->with('error', 'Please calculate delivery first.');
                    }
                }

                   $finalTotal = $subtotal + $deliveryFee;

                    try {

                   $order = DB::transaction(function () use (
                    $cart,
                    $customerId,
                    $selectedLocation,
                    $request,
                    $finalTotal,
                    $deliveryFee
                ) {

            $total = 0;

            foreach ($cart as $item) {
                $total += $item['total_amount'];
            }


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
                'status' => 'pending_payment',
                'pickup_latitude'   => session('pickup_latitude'),
                'pickup_longitude'  => session('pickup_longitude'),
                'pickup_address'    => session('pickup_address'),

                'dropoff_latitude'  => session('dropoff_latitude'),
                'dropoff_longitude' => session('dropoff_longitude'),
                'dropoff_address'   => session('dropoff_address'),
                'recipient_name'    => session('recipient_name'),
                'recipient_mobile'  => session('recipient_mobile'),
                'delivery_notes'    => session('delivery_notes'),
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
    return back()->with('error', 'Checkout failed: ' . $e->getMessage()); // ✅ see actual error
}

    
}

public function selectLocation(Request $request)
{
    
    $location = Location::findOrFail($request->location_id);
    
    session([
        'selected_location'   => $location->id,
        'pickup_latitude'     => $location->latitude,
        'pickup_longitude'    => $location->longitude,
        'pickup_address'      => $location->address,
    ]);

    

    return back()->with('success', 'Location selected successfully.');
}



public function cart()
{
    $cart = Session::get('cart', []);
    
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['total_amount'];
    }

    return view('store.cart', compact('cart', 'subtotal'));
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
     

      public function estimateDelivery(Request $request)
{
    

    $pickupLatitude  = session('pickup_latitude');
    $pickupLongitude = session('pickup_longitude');

if (!$pickupLatitude || !$pickupLongitude) {
    return response()->json([
        'success' => false,
        'message' => 'Pickup coordinates are missing. Please select a pickup location.'
    ]);
}

    if (!session('selected_location')) {
        return response()->json([
            'success' => false,
            'message' => 'Select location first.'
        ]);
    }

    $request->validate([
        'dropoff_latitude'  => 'required',
        'dropoff_longitude' => 'required',
        'dropoff_address'   => 'required',
        'recipient_name'    => 'required',
        'recipient_mobile'  => 'required',
    ]);

    try {

        $pickupLatitude  = session('pickup_latitude');
        $pickupLongitude = session('pickup_longitude');

        if (!$pickupLatitude || !$pickupLongitude) {
            return response()->json([
                'success' => false,
                'message' => 'Pickup coordinates are missing. Please select a pickup location.'
            ]);
        }

        $little = new LittleApiService();

        $response = $little->estimate([
            'pickup_latitude'   => $pickupLatitude,
            'pickup_longitude'  => $pickupLongitude,
            'dropoff_latitude'  => $request->dropoff_latitude,
            'dropoff_longitude' => $request->dropoff_longitude,
            'customer_name'     => $request->recipient_name,
            'customer_phone'    => $request->recipient_mobile,
        ]);

        if (!$response['success']) {
            return response()->json([
                'success' => false,
                'message' => $response['error'] ?? 'Unable to calculate delivery.'
            ]);
        }

        // Save in session
        session([
            'delivery_fee'      => $response['fee'],
            'dropoff_latitude'  => $request->dropoff_latitude,
            'dropoff_longitude' => $request->dropoff_longitude,
            'dropoff_address'   => $request->dropoff_address,
            'recipient_name'    => $request->recipient_name,
            'recipient_mobile'  => $request->recipient_mobile,
        ]);

        // Calculate cart subtotal
        $cart = session('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['total_amount'];
        }

        $total = $subtotal + $response['fee'];

        return response()->json([
            'success' => true,
            'delivery_fee' => $response['fee'],
            'subtotal' => $subtotal,
            'total' => $total
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Server error.'
        ]);
    }
}

}