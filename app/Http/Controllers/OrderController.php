<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderItem;
use App\Payment;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Product::where('quantity', '>', 0)
                        ->orderBy('product_name');

        if (!$user->isAdmin()) {
            $query->where('location_id', $user->location_id);
        }

        $products = $query->get();
    
        return view('orders.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,littlepay'
        ]);

        try {

            $user = auth()->user();

            $order = DB::transaction(function () use ($request, $user) {

                $total = 0;

               
                        foreach ($request->items as $item) {

                            $productQuery = Product::where('id', $item['product_id']);

                            if (!$user->isAdmin()) {
                                $productQuery->where('location_id', $user->location_id);
                            }

                            $product = $productQuery->lockForUpdate()->firstOrFail();

                            if ($product->quantity < $item['quantity']) {
                                throw new \Exception('Insufficient stock for ' . $product->product_name);
                            }

                            $total += $product->price * $item['quantity'];
                        }

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'user_id' => $user->id,
                    'customer_id' => null,
                    'location_id' => $user->location_id,
                    'source' => 'pos',
                    'total' => $total,
                    'status' => $request->payment_method === 'cash'
                        ? 'completed'     // ✅ FIXED
                        : 'pending_payment'
                ]);

                foreach ($request->items as $item) {

                    $product = Product::findOrFail($item['product_id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'amount' => $product->price * $item['quantity'],
                        'discount' => 0,
                    ]);

                    if ($request->payment_method === 'cash') {
                        $product->decrement('quantity', $item['quantity']);
                    }
                }

                return $order;
            });

            if ($request->payment_method === 'cash') {

                Payment::create([
                    'order_id' => $order->id,
                    'method' => 'cash',
                    'transaction_reference' => 'POS-' . strtoupper(Str::random(12)),
                    'amount' => $order->total,
                    'status' => 'success'
                ]);

                

                return redirect()->route('orders.show', $order->id)
                                 ->with('success', 'Cash payment completed.');
            }

            return redirect()->route('payments.initiate', $order->id);

        } catch (\Exception $e) {

            Log::error('Order Creation Failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'payments'])
                      ->findOrFail($id);

        return view('orders.receipt', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
  {
    $request->validate([
        'status' => 'required|in:processing,completed,cancelled',
        'delivery_status' => 'nullable|in:pending,out_for_delivery,delivered'
    ]);

    $order->status = $request->status;

    if ($order->source === 'online' && $request->delivery_status) {
        $order->delivery_status = $request->delivery_status;
    }

    $order->save();

    return back()->with('success','Order updated.');
   }


     public function dispatchRider(Order $order)
    {
    try {

        // ✅ Only dispatch for rider delivery orders
        if ($order->delivery_method !== 'rider') {
            return back()->with('error', 'This order does not require a rider.');
        }

        // ✅ Prevent double dispatch
        if ($order->rider_reference) {
            return back()->with('info', 'Rider already dispatched for this order.');
        }

        $little = new \App\Services\LittleApiService();

        $response = $little->requestRide([
            'order_id'          => $order->id,
            'recipient_name'    => $order->recipient_name,
            'recipient_mobile'  => $order->recipient_mobile,
            'dropoff_address'   => $order->dropoff_address,
            'dropoff_latitude'  => $order->dropoff_latitude,
            'dropoff_longitude' => $order->dropoff_longitude,
            'pickup_latitude'   => $order->pickup_latitude,
            'pickup_longitude'  => $order->pickup_longitude,
            'pickup_address'    => $order->pickup_address,
            'delivery_notes'    => $order->delivery_notes,
        ]);

        if (!$response['success']) {
            return back()->with('error', 'Rider dispatch failed: ' . $response['error']);
        }

        // ✅ Save rider reference from response
        $raw = $response['raw'];

        $order->update([
            'rider_reference' => $raw['tripId'] ?? $raw['rideId'] ?? $raw['id'] ?? null,
            'delivery_status' => 'dispatched',
        ]);

        return back()->with('success', 'Rider dispatched successfully!');

    } catch (\Exception $e) {
        return back()->with('error', 'Dispatch failed: ' . $e->getMessage());
    }
   }


     public function rideWebhook(Request $request)
{
    Log::info('Little Ride Webhook:', $request->all());

    try {
        $data = $request->all();

        // ✅ Little API uses 'tripId' not 'rideId'
        $tripId = $data['tripId'] ?? null;

        if (!$tripId) {
            return response()->json(['error' => 'No trip ID'], 400);
        }

        // ✅ Find order by rider_reference
        $order = Order::where('rider_reference', $tripId)->first();

        if (!$order) {
            Log::error('Ride Webhook: Order not found for tripId: ' . $tripId);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // ✅ Little API uses 'tripStatus' not 'status'
        $status = strtoupper($data['tripStatus'] ?? '');

        // ✅ Map Little API statuses to your delivery_status
        if ($status === 'ARRIVED') {
            $deliveryStatus = 'picking_up'; // driver arrived at pickup
        } elseif ($status === 'STARTED') {
            $deliveryStatus = 'picked_up'; // driver picked up parcel
        } elseif ($status === 'ENDED') {
            $deliveryStatus = 'delivered'; // delivery completed
        } elseif ($status === 'CANCELLED') {
            $deliveryStatus = 'cancelled';
        } elseif ($status === 'PARTIAL-DELIVERY') {
            $deliveryStatus = 'picked_up'; // partial delivery in progress
        } else {
            $deliveryStatus = $order->delivery_status;
        }

        // ✅ Update delivery status
        $order->update([
            'delivery_status' => $deliveryStatus,
        ]);

        // ✅ If delivered — mark order as completed
        if ($deliveryStatus === 'delivered') {
            $order->update(['status' => 'completed']);
        }

        // ✅ If cancelled — reset so cashier can re-dispatch
        if ($deliveryStatus === 'cancelled') {
            $order->update([
                'rider_reference' => null,
                'rider_id'        => null,
                'rider_name'      => null,
                'rider_mobile'    => null,
                'delivery_status' => 'pending',
            ]);

            Log::info('Ride cancelled: ' . ($data['Message'] ?? 'No reason given'));
        }

        return response()->json(['message' => 'Ride status updated']);

    } catch (\Exception $e) {
        Log::error('Ride Webhook Error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}
}