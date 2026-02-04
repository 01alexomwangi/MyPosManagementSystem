<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\SaleItem;
use App\Product;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch products from the real products table
        if ($user->isAdmin()) {
            $products = Product::where('quantity', '>', 0)
                               ->orderBy('product_name')
                               ->get();
        } else {
            $products = Product::where('location_id', $user->location_id)
                               ->where('quantity', '>', 0)
                               ->orderBy('product_name')
                               ->get();
        }

        return view('sales.index', compact('products'));
    }

    public function store(Request $request)
    {
        $sale = DB::transaction(function () use ($request) {

            $user = auth()->user();
            $total = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $total += $product->price * $item['quantity'];
            }

            $sale = Sale::create([
                'user_id' => $user->id,
                'location_id' => $user->location_id,
                'total' => $total,
                'paid' => $request->paid_amount,
                'balance' => $request->paid_amount - $total,
                'customer_name' => $request->customer_name ?? null,
                'customer_phone' => $request->customer_phone ?? null,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $product->decrement('quantity', $item['quantity']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale->id);
    }

    public function show($id)
    {
        $sale = Sale::with(['items.product', 'user', 'location'])->findOrFail($id);
        return view('sales.receipt', compact('sale'));
    }
}