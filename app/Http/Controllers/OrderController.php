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

                    $product = Product::where('id', $item['product_id'])
                                      ->where('location_id', $user->location_id)
                                      ->lockForUpdate()
                                      ->firstOrFail();

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
                        ? 'paid'
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

                    // Deduct stock only for cash
                    if ($request->payment_method === 'cash') {
                        $product->decrement('quantity', $item['quantity']);
                    }
                }

                return $order;
            });

            // 💰 CASH PAYMENT
            if ($request->payment_method === 'cash') {

                Payment::create([
                    'order_id' => $order->id,
                    'method' => 'cash',
                    'transaction_reference' => 'POS-' . strtoupper(Str::random(12)),
                    'amount' => $order->total,
                    'status' => 'paid'
                ]);

                return redirect()->route('orders.show', $order->id)
                                 ->with('success', 'Cash payment completed.');
            }

            // 🌐 LittlePay
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
}
