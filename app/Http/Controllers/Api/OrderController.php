<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderItem;
use App\Payment;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // ✅ GET ALL ORDERS
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $orders = Order::with(['items.product', 'payments', 'location'])
                           ->orderBy('created_at', 'desc')
                           ->get();
        } else {
            $orders = Order::with(['items.product', 'payments', 'location'])
                           ->where('location_id', $user->location_id)
                           ->orderBy('created_at', 'desc')
                           ->get();
        }

        return response()->json([
            'success' => true,
            'total'   => $orders->count(),
            'orders'  => $orders->map(function($order) {
                return [
                    'id'             => $order->id,
                    'order_number'   => $order->order_number,
                    'status'         => $order->status,
                    'source'         => $order->source,
                    'total'          => $order->total,
                    'location'       => $order->location->name ?? 'N/A',
                    'delivery_method'=> $order->delivery_method,
                    'delivery_status'=> $order->delivery_status,
                    'payment'        => $order->payments->first() ? [
                        'method' => $order->payments->first()->method,
                        'status' => $order->payments->first()->status,
                        'amount' => $order->payments->first()->amount,
                    ] : null,
                    'items_count'    => $order->items->count(),
                    'created_at'     => $order->created_at->format('Y-m-d H:i'),
                ];
            })
        ]);
    }

    // ✅ GET SINGLE ORDER
    public function show($id)
    {
        $order = Order::with(['items.product', 'payments', 'location'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order'   => [
                'id'             => $order->id,
                'order_number'   => $order->order_number,
                'status'         => $order->status,
                'source'         => $order->source,
                'total'          => $order->total,
                'location'       => $order->location->name ?? 'N/A',
                'delivery_method'=> $order->delivery_method,
                'delivery_status'=> $order->delivery_status,
                'recipient_name' => $order->recipient_name,
                'recipient_mobile'=> $order->recipient_mobile,
                'dropoff_address'=> $order->dropoff_address,
                'delivery_notes' => $order->delivery_notes,
                'rider_name'     => $order->rider_name,
                'rider_mobile'   => $order->rider_mobile,
                'payment'        => $order->payments->first() ? [
                    'id'     => $order->payments->first()->id,
                    'method' => $order->payments->first()->method,
                    'status' => $order->payments->first()->status,
                    'amount' => $order->payments->first()->amount,
                ] : null,
                'items'          => $order->items->map(function($item) {
                    return [
                        'product'    => $item->product->product_name ?? 'N/A',
                        'quantity'   => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'amount'     => $item->amount,
                    ];
                }),
                'created_at'     => $order->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    // ✅ CREATE ORDER (POS)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|numeric|min:1',
            'payment_method'         => 'required|in:cash,littlepay',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();

            $order = DB::transaction(function () use ($request, $user) {

                $total = 0;

                // ✅ Validate stock
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

                // ✅ Create order
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'user_id'      => $user->id,
                    'location_id'  => $user->location_id,
                    'source'       => 'pos',
                    'total'        => $total,
                    'status'       => $request->payment_method === 'cash'
                                        ? 'completed'
                                        : 'pending_payment',
                ]);

                // ✅ Create order items
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'unit_price' => $product->price,
                        'amount'     => $product->price * $item['quantity'],
                        'discount'   => 0,
                    ]);

                    // ✅ Deduct stock for cash payments
                    if ($request->payment_method === 'cash') {
                        $product->decrement('quantity', $item['quantity']);
                    }
                }

                return $order;
            });

            // ✅ Create payment record for cash
            if ($request->payment_method === 'cash') {
                Payment::create([
                    'order_id'              => $order->id,
                    'method'                => 'cash',
                    'transaction_reference' => 'POS-' . strtoupper(Str::random(12)),
                    'amount'                => $order->total,
                    'status'                => 'success',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order'   => [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'total'        => $order->total,
                    'status'       => $order->status,
                    'payment_method' => $request->payment_method,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Order Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ UPDATE ORDER STATUS
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:processing,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'order'   => [
                'id'     => $order->id,
                'status' => $order->status,
            ]
        ]);
    }// ✅ CREATE ONLINE ORDER (with delivery)
public function storeOnline(Request $request)
{
    $validator = Validator::make($request->all(), [
        'items'                  => 'required|array|min:1',
        'items.*.product_id'     => 'required|exists:products,id',
        'items.*.quantity'       => 'required|numeric|min:1',
        'payment_method'         => 'required|in:cash,littlepay',
        'delivery_method'        => 'required|in:pickup,rider',

        // ✅ Required for rider delivery
        'recipient_name'         => 'required_if:delivery_method,rider',
        'recipient_mobile'       => 'required_if:delivery_method,rider',
        'dropoff_address'        => 'required_if:delivery_method,rider',
        'dropoff_latitude'       => 'required_if:delivery_method,rider',
        'dropoff_longitude'      => 'required_if:delivery_method,rider',
        'delivery_fee'           => 'required_if:delivery_method,rider|numeric',
        'delivery_notes'         => 'nullable|string',

        // ✅ Pickup coordinates
        'pickup_address'         => 'required|string',
        'pickup_latitude'        => 'required',
        'pickup_longitude'       => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        $user = auth()->user();

        $order = DB::transaction(function () use ($request, $user) {

            $total = 0;

            // ✅ Validate stock
            foreach ($request->items as $item) {
                $product = Product::where('id', $item['product_id'])
                                  ->lockForUpdate()
                                  ->firstOrFail();

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for ' . $product->product_name);
                }

                $total += $product->price * $item['quantity'];
            }

            // ✅ Add delivery fee for rider
            $deliveryFee = $request->delivery_method === 'rider'
                           ? $request->delivery_fee
                           : 0;

            $total += $deliveryFee;

            // ✅ Create order
            $order = Order::create([
                'order_number'     => 'ORD-' . strtoupper(uniqid()),
                'user_id'          => $user->id,
                'location_id'      => $user->location_id,
                'source'           => 'online',
                'total'            => $total,
                'status'           => 'pending_payment',
                'delivery_method'  => $request->delivery_method,
                'delivery_fee'     => $deliveryFee,
                'delivery_status'  => $request->delivery_method === 'rider' ? 'pending' : null,
                'pickup_address'   => $request->pickup_address,
                'pickup_latitude'  => $request->pickup_latitude,
                'pickup_longitude' => $request->pickup_longitude,
                'recipient_name'   => $request->recipient_name,
                'recipient_mobile' => $request->recipient_mobile,
                'dropoff_address'  => $request->dropoff_address,
                'dropoff_latitude' => $request->dropoff_latitude,
                'dropoff_longitude'=> $request->dropoff_longitude,
                'delivery_notes'   => $request->delivery_notes,
            ]);

            // ✅ Create order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                    'amount'     => $product->price * $item['quantity'],
                    'discount'   => 0,
                ]);
            }

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Online order created successfully',
            'order'   => [
                'id'              => $order->id,
                'order_number'    => $order->order_number,
                'total'           => $order->total,
                'status'          => $order->status,
                'delivery_method' => $order->delivery_method,
                'delivery_fee'    => $order->delivery_fee,
                'delivery_status' => $order->delivery_status,
                'payment_method'  => $request->payment_method,
            ]
        ], 201);

    } catch (\Exception $e) {
        Log::error('API Online Order Failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


}