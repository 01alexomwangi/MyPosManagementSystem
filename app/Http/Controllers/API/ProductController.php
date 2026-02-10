<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        $products = Product::with(['brand', 'category', 'location'])->get();

        return response()->json([
            'data' => $products
        ], 200);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with(['brand', 'category', 'location'])->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'data' => $product
        ], 200);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'alert_stock' => 'required|integer',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created',
            'data' => $product
        ], 201);
    }

    // PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated',
            'data' => $product
        ], 200);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted'
        ], 200);
    }

}