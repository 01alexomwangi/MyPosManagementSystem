<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use App\Category;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $products = Product::with(['location', 'brand', 'category'])->paginate(8);
            $locations = Location::orderBy('name')->get();
        } else {
            $products = Product::where('location_id', $user->location_id)
                ->with(['location', 'brand', 'category'])
                ->paginate(8);
            $locations = Location::where('id', $user->location_id)->get();
        }

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'brands', 'categories', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'location_id' => 'nullable|exists:locations,id',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'alert_stock' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
        }

        Product::create([
            'product_name' => $request->product_name,
            'location_id' => $request->location_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'alert_stock' => $request->alert_stock,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return redirect()->back()->with('success', 'Product added successfully');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required',
            'location_id' => 'nullable|exists:locations,id',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'alert_stock' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }

            $imageName = time() . '_' . Str::random(10) . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $product->image = $imageName;
        }

        // Update other fields
        $product->update($request->except('image'));

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
            unlink(public_path('images/products/' . $product->image));
        }

        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }
}
