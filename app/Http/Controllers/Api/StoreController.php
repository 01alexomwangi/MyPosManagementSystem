<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Customer;
use App\Location;
use App\Order;
use App\OrderItem;
use App\Product;
use App\Services\LittleApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    // ✅ GET PRODUCTS
    public function products(Request $request)
    {
        $query = Product::with(['location', 'brand', 'category'])
                        ->where('quantity', '>', 0);

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('product_name')->get();

        return response()->json([
            'success'  => true,
            'total'    => $products->count(),
            'products' => $products->map(function($product) {
                return [
                    'id'           => $product->id,
                    'product_name' => $product->product_name,
                    'price'        => $product->price,
                    'quantity'     => $product->quantity,
                    'description'  => $product->description,
                    'image'        => $product->image ? url('images/products/' . $product->image) : null,
                    'brand'        => $product->brand->name ?? 'N/A',
                    'category'     => $product->category->name ?? 'N/A',
                    'location_id'  => $product->location_id,
                    'location'     => $product->location->name ?? 'N/A',
                ];
            })
        ]);
    }

    // ✅ GET SINGLE PRODUCT
    public function product($id)
    {
        $product = Product::with(['location', 'brand', 'category'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id'           => $product->id,
                'product_name' => $product->product_name,
                'price'        => $product->price,
                'quantity'     => $product->quantity,
                'description'  => $product->description,
                'image'        => $product->image ? url('images/products/' . $product->image) : null,
                'brand'        => $product->brand->name ?? 'N/A',
                'category'     => $product->category->name ?? 'N/A',
                'location_id'  => $product->location_id,
                'location'     => $product->location->name ?? 'N/A',
            ]
        ]);
    }

    // ✅ GET LOCATIONS
    public function locations()
    {
        $locations = Location::orderBy('name')->get();

        return response()->json([
            'success'   => true,
            'locations' => $locations->map(function($location) {
                return [
                    'id'        => $location->id,
                    'name'      => $location->name,
                    'address'   => $location->address,
                    'latitude'  => $location->latitude,
                    'longitude' => $location->longitude,
                ];
            })
        ]);
    }

    // ✅ CUSTOMER REGISTER
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|min:6|confirmed',
            'phone'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

            $tokenResult = $customer->createToken('store_token');
            $tokenResult->accessToken->expires_at = now()->addHours(24);
            $tokenResult->accessToken->save();
            $token = $tokenResult->plainTextToken;

        return response()->json([
            'success'  => true,
            'message'  => 'Registration successful',
            'token'    => $token,
            'customer' => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'email' => $customer->email,
            ]
        ], 201);
    }

    // ✅ CUSTOMER LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $customer->tokens()->delete();
        
            $tokenResult = $customer->createToken('store_token');
            $tokenResult->accessToken->expires_at = now()->addHours(24);
            $tokenResult->accessToken->save();
            $token = $tokenResult->plainTextToken;

        return response()->json([
            'success'  => true,
            'message'  => 'Login successful',
            'token'    => $token,
            'customer' => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
            ]
        ]);
    }

    // ✅ CUSTOMER LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // ✅ ESTIMATE DELIVERY
    public function estimateDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_latitude'   => 'required',
            'pickup_longitude'  => 'required',
            'dropoff_latitude'  => 'required',
            'dropoff_longitude' => 'required',
            'recipient_name'    => 'required',
            'recipient_mobile'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $little   = new LittleApiService();
            $response = $little->estimate([
                'pickup_latitude'   => $request->pickup_latitude,
                'pickup_longitude'  => $request->pickup_longitude,
                'dropoff_latitude'  => $request->dropoff_latitude,
                'dropoff_longitude' => $request->dropoff_longitude,
                'customer_name'     => $request->recipient_name,
                'customer_phone'    => $request->recipient_mobile,
            ]);

            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $response['error'] ?? 'Unable to calculate delivery.'
                ], 400);
            }

            return response()->json([
                'success'      => true,
                'delivery_fee' => $response['fee'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error.'
            ], 500);
        }
    }

    // ✅ CHECKOUT
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|numeric|min:1',
            'location_id'            => 'required|exists:locations,id',
            'delivery_method'        => 'required|in:pickup,rider',
            'delivery_fee'           => 'required_if:delivery_method,rider|numeric',
            'pickup_latitude'        => 'required',
            'pickup_longitude'       => 'required',
            'pickup_address'         => 'required',
            'recipient_name'         => 'required_if:delivery_method,rider',
            'recipient_mobile'       => 'required_if:delivery_method,rider',
            'dropoff_address'        => 'required_if:delivery_method,rider',
            'dropoff_latitude'       => 'required_if:delivery_method,rider',
            'dropoff_longitude'      => 'required_if:delivery_method,rider',
            'delivery_notes'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $customer = $request->user();

            $order = DB::transaction(function () use ($request, $customer) {
                $subtotal    = 0;
                $deliveryFee = $request->delivery_method === 'rider' ? $request->delivery_fee : 0;

                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['product_id'])
                                      ->where('location_id', $request->location_id)
                                      ->lockForUpdate()
                                      ->firstOrFail();

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception('Insufficient stock for ' . $product->product_name);
                    }

                    $subtotal += $product->price * $item['quantity'];
                }

                $order = Order::create([
                    'order_number'     => 'ORD-' . strtoupper(uniqid()),
                    'user_id'          => null,
                    'customer_id'      => $customer->id,
                    'location_id'      => $request->location_id,
                    'source'           => 'online',
                    'total'            => $subtotal + $deliveryFee,
                    'delivery_method'  => $request->delivery_method,
                    'delivery_fee'     => $deliveryFee,
                    'delivery_status'  => 'pending',
                    'status'           => 'pending_payment',
                    'pickup_latitude'  => $request->pickup_latitude,
                    'pickup_longitude' => $request->pickup_longitude,
                    'pickup_address'   => $request->pickup_address,
                    'dropoff_latitude' => $request->dropoff_latitude,
                    'dropoff_longitude'=> $request->dropoff_longitude,
                    'dropoff_address'  => $request->dropoff_address,
                    'recipient_name'   => $request->recipient_name,
                    'recipient_mobile' => $request->recipient_mobile,
                    'delivery_notes'   => $request->delivery_notes,
                ]);

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
                'message' => 'Order created successfully',
                'order'   => [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'total'        => $order->total,
                    'status'       => $order->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ GET CUSTOMER ORDERS
    public function orders(Request $request)
    {
        $customer = $request->user();

        $orders = Order::with(['items.product', 'payments'])
                       ->where('customer_id', $customer->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success' => true,
            'orders'  => $orders->map(function($order) {
                return [
                    'id'              => $order->id,
                    'order_number'    => $order->order_number,
                    'status'          => $order->status,
                    'total'           => $order->total,
                    'delivery_method' => $order->delivery_method,
                    'delivery_status' => $order->delivery_status,
                    'payment'         => $order->payments->first() ? [
                        'method' => $order->payments->first()->method,
                        'status' => $order->payments->first()->status,
                    ] : null,
                    'items_count'     => $order->items->count(),
                    'created_at'      => $order->created_at->format('Y-m-d H:i'),
                ];
            })
        ]);
    }

    // ✅ GET SINGLE ORDER
    public function order($id)
    {
        $customer = auth()->user();

        $order = Order::with(['items.product', 'payments'])
                      ->where('customer_id', $customer->id)
                      ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order'   => [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'status'           => $order->status,
                'total'            => $order->total,
                'delivery_method'  => $order->delivery_method,
                'delivery_status'  => $order->delivery_status,
                'delivery_fee'     => $order->delivery_fee,
                'recipient_name'   => $order->recipient_name,
                'recipient_mobile' => $order->recipient_mobile,
                'dropoff_address'  => $order->dropoff_address,
                'payment'          => $order->payments->first() ? [
                    'method' => $order->payments->first()->method,
                    'status' => $order->payments->first()->status,
                    'amount' => $order->payments->first()->amount,
                ] : null,
                'items'            => $order->items->map(function($item) {
                    return [
                        'product'    => $item->product->product_name ?? 'N/A',
                        'quantity'   => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'amount'     => $item->amount,
                    ];
                }),
                'created_at'       => $order->created_at->format('Y-m-d H:i'),
            ]
        ]);
    }
}