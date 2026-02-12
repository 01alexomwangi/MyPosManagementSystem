<?php

namespace App\Http\Controllers;

use App\Product;

use Illuminate\Http\Request;

class StoreController extends Controller
{
     public function index(Request $request)
    {
    $query = Product::query();

    if ($request->filled('search')) {
    $query->where(function($q) use ($request) {
        $q->where('product_name', 'like', '%' . $request->search . '%')
          ->orWhere('description', 'like', '%' . $request->search . '%');
    });
   }

    $products = $query->paginate(12);

    return view('store.index', compact('products'));
        
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('store.show', compact('product'));
    }


}
