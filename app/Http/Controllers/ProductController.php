<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\Category;
use App\Location;
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
       $user = auth()->user();

    if ($user->isAdmin()) {
        // Admin sees all products
        $products = Product::with('location')->paginate(8);
        $locations = Location::orderBy('name')->get();
    } else {
        // User sees only products in their location
        $products = Product::where('location_id', $user->location_id)
                           ->with('location')
                           ->paginate(8);

        // User should only see their location
        $locations = Location::where('id', $user->location_id)->get();
    }

    $brands = Brand::orderBy('name')->get();
    $categories = Category::orderBy('name')->get();

    return view('products.index', compact(
        'products',
        'brands',
        'categories',
        'locations'
    ));
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
        'location_id' => 'nullable|exists:locations,id',
        'brand_id' => 'required|exists:brands,id',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'alert_stock' => 'required|integer',
        'description' => 'nullable|string',
    ]);

    Product::create([
        'product_name' => $request->product_name,
        'location_id' => $request->location_id,
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
