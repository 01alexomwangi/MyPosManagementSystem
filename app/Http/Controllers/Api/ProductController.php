<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Product;
use App\Brand;
use App\Category;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // ✅ GET ALL PRODUCTS — everyone
    public function index(Request $request)
    {
        $user = auth()->user();

        // Admin sees all products
        // Cashier/Manager sees only their location
        if ($user->isAdmin()) {
            $products = Product::with(['location', 'brand', 'category'])->get();
        } else {
            $products = Product::where('location_id', $user->location_id)
                               ->with(['location', 'brand', 'category'])
                               ->get();
        }

        return response()->json([
            'success' => true,
            'total'   => $products->count(),
            'products' => $products->map(function($product) {
                return [
                    'id'           => $product->id,
                    'product_name' => $product->product_name,
                    'price'        => $product->price,
                    'quantity'     => $product->quantity,
                    'alert_stock'  => $product->alert_stock,
                    'description'  => $product->description,
                    'image'        => $product->image ? url('images/products/' . $product->image) : null,
                    'brand'        => $product->brand->name ?? 'N/A',
                    'category'     => $product->category->name ?? 'N/A',
                    'location'     => $product->location->name ?? 'N/A',
                ];
            })
        ]);
    }

    // ✅ GET SINGLE PRODUCT — everyone
    public function show($id)
    {
        $product = Product::with(['location', 'brand', 'category'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id'           => $product->id,
                'product_name' => $product->product_name,
                'price'        => $product->price,
                'quantity'     => $product->quantity,
                'alert_stock'  => $product->alert_stock,
                'description'  => $product->description,
                'image'        => $product->image ? url('images/products/' . $product->image) : null,
                'brand'        => $product->brand->name ?? 'N/A',
                'category'     => $product->category->name ?? 'N/A',
                'location'     => $product->location->name ?? 'N/A',
            ]
        ]);
    }

    // ✅ CREATE PRODUCT — admin/manager only
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string',
            'brand_id'     => 'required|exists:brands,id',
            'category_id'  => 'nullable|exists:categories,id',
            'location_id'  => 'nullable|exists:locations,id',
            'price'        => 'required|numeric',
            'quantity'     => 'required|integer',
            'alert_stock'  => 'required|integer',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
        }

        $product = Product::create([
            'product_name' => $request->product_name,
            'brand_id'     => $request->brand_id,
            'category_id'  => $request->category_id,
            'location_id'  => $request->location_id,
            'price'        => $request->price,
            'quantity'     => $request->quantity,
            'alert_stock'  => $request->alert_stock,
            'description'  => $request->description,
            'image'        => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => [
                'id'           => $product->id,
                'product_name' => $product->product_name,
                'price'        => $product->price,
                'quantity'     => $product->quantity,
                'location'     => $product->location_id,
            ]
        ], 201);
    }

    // ✅ UPDATE PRODUCT — admin/manager only
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_name' => 'sometimes|required|string',
            'brand_id'     => 'sometimes|required|exists:brands,id',
            'category_id'  => 'nullable|exists:categories,id',
            'location_id'  => 'nullable|exists:locations,id',
            'price'        => 'sometimes|required|numeric',
            'quantity'     => 'sometimes|required|integer',
            'alert_stock'  => 'sometimes|required|integer',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('image')) {
            // ✅ Delete old image
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }

            $imageName = time() . '_' . Str::random(10) . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $product->image = $imageName;
            $product->save();
        }

        $product->update($request->except('image'));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => [
                'id'           => $product->id,
                'product_name' => $product->product_name,
                'price'        => $product->price,
                'quantity'     => $product->quantity,
                'location'     => $product->location_id,
            ]
        ]);
    }

    // ✅ DELETE PRODUCT — admin only
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
            unlink(public_path('images/products/' . $product->image));
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}