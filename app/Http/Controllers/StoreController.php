<?php

namespace App\Http\Controllers;

use App\Product;

use Illuminate\Http\Request;

class StoreController extends Controller
{
     public function index()
    {
        $products = Product::where('status', 1)->paginate(12);
        return view('store.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('store.show', compact('product'));
    }
}
