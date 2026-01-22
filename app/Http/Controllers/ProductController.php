<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
    {
    $products = Product::paginate(5);
    // dd($products);
    $brands = Brand::orderBy('name')->get();
    $categories = Category::orderBy('name')->get();
    return view('products.index', compact('products', 'brands','categories'));
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
    $request->validate([
        'product_name' => 'required',
        'brand_id' => 'required|exists:brands,id',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'alert_stock' => 'required|integer',
        'description' => 'nullable|string',
    ]);

    Product::create([
        'product_name' => $request->product_name,
        'category_id' => $request->category_id,
        'brand_id' => $request->brand_id,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'alert_stock' => $request->alert_stock,
        'description' => $request->description,
    ]);

    return redirect()->back()->with('success', 'Product added successfully');
}



    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->all());

        return redirect()->back()->with('success','Product Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back()->with('success','Product Deleted successfully!');
    }
}
