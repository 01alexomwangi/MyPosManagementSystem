<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PendingSale;
use App\Sale;
use App\SaleItem;
use App\Product;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    /**
     * Show pending sales for the logged-in cashier's location only
     */
    public function pendingSales()
    {
        //dd(\App\PendingSale::all()->toArray());

       // (auth()->user()->location_id);

        // 1️⃣ Get cashier's location
      $user = auth()->user();

        $pendingSales = PendingSale::with(['customer', 'items.product'])
            ->where('status', 'pending');

        // If user is not admin, filter by their location
        if (!$user->hasRole('admin')) {
            $pendingSales->where('location_id', $user->location_id);
        }

$pendingSales = $pendingSales->orderBy('created_at','desc')->get();

        // 3️⃣ Return correct view
        return view('cashier.pending_sales', compact('pendingSales'));
    }

    /**
     * Complete a pending sale (ONLY if it belongs to cashier's location)
     */
    public function completeSale($id)
{
    $cashierLocationId = auth()->user()->location_id;

    DB::transaction(function () use ($id, $cashierLocationId) {

        $pendingSale = PendingSale::with(['items.product', 'customer'])
            ->where('id', $id)
            ->where('location_id', $cashierLocationId)
            ->where('status', 'pending')
            ->firstOrFail();

        $sale = Sale::create([
            'user_id'        => auth()->id(),
            'location_id'    => $pendingSale->location_id,
            'total'          => $pendingSale->total,
            'paid'           => $pendingSale->total,
            'balance'        => 0,
            'customer_name'  => optional($pendingSale->customer)->name,
            'customer_phone' => optional($pendingSale->customer)->phone,
            'payment_method'=> 'cash',
        ]);

        foreach ($pendingSale->items as $item) {

            SaleItem::create([
                'sale_id'   => $sale->id,
                'product_id'=> $item->product_id,
                'quantity'  => $item->quantity,
                'price'     => $item->price,
                'subtotal'  => $item->total_amount,
            ]);

            $product = Product::findOrFail($item->product_id);
            $product->decrement('quantity', $item->quantity);
        }

        $pendingSale->update(['status' => 'completed']);
    });

    return redirect()
        ->route('cashier.pending')
        ->with('success', 'Sale completed successfully.');
}

}
