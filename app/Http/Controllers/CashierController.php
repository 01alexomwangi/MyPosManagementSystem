<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PendingSale;
use App\PendingSaleItem;
use App\Sale;
use App\SaleItem;
use App\Product;
use DB;

class CashierController extends Controller
{
    public function pendingSales()
    {
        $pendingSales = PendingSale::with(['customer','items.product'])
            ->where('status', 'pending')
            ->get();

        return view('cashier.pending_sales', compact('pendingSales'));
    }

    public function completeSale($id)
    {
        DB::transaction(function() use($id){
            $pendingSale = PendingSale::with('items')->findOrFail($id);

            // Create Sale
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'location_id' => $pendingSale->location_id,
                'total' => $pendingSale->total,
                'paid' => $pendingSale->total,
                'balance' => 0,
                'customer_name' => $pendingSale->customer->name ?? null,
                'customer_phone' => $pendingSale->customer->phone ?? null,
                'payment_method' => 'cash',
            ]);

            // Move pending items to sale items
            foreach($pendingSale->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->total_amount,
                ]);

                // Reduce stock
                $product = Product::find($item->product_id);
                $product->decrement('quantity', $item->quantity);
            }

            // Mark pending sale as completed
            $pendingSale->update(['status' => 'completed']);
        });

        return redirect()->route('cashier.pending')->with('success', 'Sale completed successfully!');
    }
}
