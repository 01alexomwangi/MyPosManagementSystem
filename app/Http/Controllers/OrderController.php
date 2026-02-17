<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\Product;
use App\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->quantity < $item['quantity']) {
                    return back()->with('error', 'Insufficient stock for ' . $product->product_name);
                }

                $total += $product->price * $item['quantity'];
            }

            $order = DB::transaction(function () use ($request, $user, $total) {

                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'user_id' => $user->id,
                    'location_id' => $user->location_id,
                    'total' => $total,
                    'status' => 'pending_payment'
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
                }

                return $order;
            });

            // CASH FLOW
            if ($request->payment_method === 'cash') {

                DB::transaction(function () use ($order, $request) {

                    foreach ($order->items()->with('product')->get() as $item) {
                        $item->product->decrement('quantity', $item->quantity);
                    }

                    Payment::create([
                        'order_id' => $order->id,
                        'method' => 'cash',
                        'amount' => $request->paid_amount ?? $order->total,
                        'status' => 'success'
                    ]);

                    $order->update(['status' => 'paid']);
                });

                return redirect()->route('orders.show', $order->id)
                                 ->with('success', 'Cash payment completed successfully.');
            }

            // ONLINE FLOW
            return redirect()->route('payments.initiate', $order->id);

        } catch (\Exception $e) {

            Log::error('Order Creation Failed: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'payments'])
                        ->findOrFail($id);

        return view('orders.receipt', compact('order'));
    }
}
