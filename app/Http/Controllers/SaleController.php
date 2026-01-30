<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\SaleItem;
use App\Product;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $user = auth()->user();

    // Admin can see all products
    if ($user->isAdmin()) {
        $products = Product::where('quantity', '>', 0)->get();
         } else {
        // Normal user sees only products from their location
        $products = Product::where('location_id', $user->location_id)
                           ->where('quantity', '>', 0)
                           ->get();
          }

    return view('sales.index', compact('products'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sale = Sale::with(['items.product','user','location'])->findOrFail($id);
        return view('sales.receipt', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
