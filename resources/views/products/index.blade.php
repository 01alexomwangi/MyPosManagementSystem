@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- LEFT SIDE: PRODUCTS TABLE -->
        <div class="col-md-12">
            <div class="card">

                <!-- CARD HEADER -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Products</h4>
                    <div>
                        @if(auth()->user()->isAdmin())
                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addbrand">
                                <i class="fa fa-plus"></i> Add Brand
                            </a>
                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#addcategory">
                                <i class="fa fa-plus"></i> Add Category
                            </a>
                        @endif
                        <a href="#" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addproduct">
                            <i class="fa fa-plus"></i> Add Product
                        </a>
                    </div>
                </div>

                <!-- CARD BODY -->
                <div class="card-body">

                    <!-- SEARCH INPUT -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" id="productSearch" class="form-control" placeholder="Search product...">
                        </div>
                    </div>

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Alert Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="productTable">
                            @foreach($products as $key => $product)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <img src="{{ $product->image ? asset('images/products/'.$product->image) : asset('images/products/default.png') }}"
                                         alt="{{ $product->product_name }}" style="height:50px; width:50px; object-fit:cover; border-radius:5px;">
                                </td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->location->name ?? '-' }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    @if($product->alert_stock >= $product->quantity)
                                        <span class="badge bg-danger">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->alert_stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editproduct{{ $product->id }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteproduct{{ $product->id }}">
                                            <i class="fa fa-trash"></i> Del
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- EDIT PRODUCT MODAL --}}
                            <div class="modal fade" id="editproduct{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="mb-3">
                                                    <label>Product Image</label>
                                                    <input type="file" name="image" class="form-control mb-2">
                                                    @if($product->image)
                                                        <img src="{{ asset('images/products/'.$product->image) }}" alt="{{ $product->product_name }}" style="height:50px;width:50px;object-fit:cover;margin-top:5px;">
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label>Product Name</label>
                                                    <input type="text" name="product_name" class="form-control" value="{{ $product->product_name }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Brand</label>
                                                    <select name="brand_id" class="form-control">
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control">
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Location</label>
                                                    <select name="location_id" class="form-control">
                                                        <option value="">-- Select Location --</option>
                                                        @foreach($locations as $location)
                                                            <option value="{{ $location->id }}" {{ $product->location_id == $location->id ? 'selected' : '' }}>
                                                                {{ $location->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Price</label>
                                                    <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Quantity</label>
                                                    <input type="number" name="quantity" class="form-control" value="{{ $product->quantity }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Alert Stock</label>
                                                    <input type="number" name="alert_stock" class="form-control" value="{{ $product->alert_stock }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                                                </div>

                                                <button class="btn btn-primary w-100">Update Product</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DELETE PRODUCT MODAL --}}
                            <div class="modal fade" id="deleteproduct{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <p>Delete <strong>{{ $product->product_name }}</strong>?</p>
                                                <button class="btn btn-danger w-100">Yes, Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>

                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ADD BRAND MODAL --}}
<div class="modal fade" id="addbrand" tabindex="-1" aria-labelledby="addBrandLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('brands.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="brandName" class="form-label">Brand Name</label>
                        <input type="text" name="name" class="form-control" id="brandName" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Brand</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ADD CATEGORY MODAL --}}
<div class="modal fade" id="addcategory" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" id="categoryName" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ADD PRODUCT MODAL --}}
<div class="modal fade" id="addproduct" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control">
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Location</label>
                        <select name="location_id" class="form-control">
                            <option value="">-- Select Location --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Alert Stock</label>
                        <input type="number" name="alert_stock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <button class="btn btn-dark w-100">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('productSearch');
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            document.querySelectorAll('#productTable tr').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    });
</script>
@endsection
