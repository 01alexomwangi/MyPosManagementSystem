@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- LEFT SIDE: PRODUCTS TABLE -->
        <div class="col-md-9">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Products</h4>
                    <div>
                        @if(auth()->user()->isAdmin())
                            <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addbrand">
                                <i class="fa fa-plus"></i> Add Brand
                            </a>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#addcategory">
                                <i class="fa fa-plus"></i> Add Category
                            </a>
                        @endif
                        <a href="#" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#addproduct">
                            <i class="fa fa-plus"></i> Add Product
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <!-- SEARCH INPUT -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text"
                                   id="productSearch"
                                   class="form-control"
                                   placeholder="Search product...">
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
                                
                                <!-- IMAGE COLUMN -->
                                <td>
                                    <img src="{{ $product->image 
                                                ? asset('images/products/'.$product->image) 
                                                : asset('images/products/default.png') }}"
                                         alt="{{ $product->product_name }}"
                                         style="height:50px; width:50px; object-fit:cover; border-radius:5px;">
                                </td>

                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->location->name ?? '-' }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    @if($product->alert_stock >= $product->quantity)
                                        <span class="badge badge-danger">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">{{ $product->alert_stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-info btn-sm"
                                           data-toggle="modal"
                                           data-target="#editproduct{{ $product->id }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm"
                                           data-toggle="modal"
                                           data-target="#deleteproduct{{ $product->id }}">
                                            <i class="fa fa-trash"></i> Del
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- EDIT PRODUCT MODAL --}}
                            <div class="modal right fade" id="editproduct{{ $product->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4>Edit Product</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="form-group">
                                                    <label>Product Image</label>
                                                    <input type="file" name="image" class="form-control mb-2">
                                                    @if($product->image)
                                                        <img src="{{ asset('images/products/'.$product->image) }}" 
                                                             alt="{{ $product->product_name }}" 
                                                             style="height:50px; width:50px; object-fit:cover; margin-top:5px;">
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    <label>Product Name</label>
                                                    <input type="text" name="product_name" class="form-control"
                                                           value="{{ $product->product_name }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Brand</label>
                                                    <select name="brand_id" class="form-control">
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}"
                                                                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control">
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Location</label>
                                                    <select name="location_id" class="form-control">
                                                        <option value="">-- Select Location --</option>
                                                        @foreach($locations as $location)
                                                            <option value="{{ $location->id }}"
                                                                {{ $product->location_id == $location->id ? 'selected' : '' }}>
                                                                {{ $location->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="number" name="price" class="form-control"
                                                           value="{{ $product->price }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="number" name="quantity" class="form-control"
                                                           value="{{ $product->quantity }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Alert Stock</label>
                                                    <input type="number" name="alert_stock" class="form-control"
                                                           value="{{ $product->alert_stock }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                                                </div>

                                                <button class="btn btn-primary btn-block">Update Product</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DELETE PRODUCT MODAL --}}
                            <div class="modal right fade" id="deleteproduct{{ $product->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4>Delete Product</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <p>Delete <strong>{{ $product->product_name }}</strong>?</p>
                                                <button class="btn btn-danger btn-block">Yes, Delete</button>
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

{{-- ADD PRODUCT MODAL --}}
<div class="modal right fade" id="addproduct" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Add Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control">
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <select name="location_id" class="form-control">
                            <option value="">-- Select Location --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Alert Stock</label>
                        <input type="number" name="alert_stock" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <button class="btn btn-primary btn-block">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SEARCH SCRIPT --}}
<script>
    $(document).ready(function () {
        $('#productSearch').on('keyup', function () {
            let value = $(this).val().toLowerCase();
            $('#productTable tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

<style>
.modal.right .modal-dialog {
    top: 0;
    right: 0;
    margin-right: 19vh;
}
.modal.fade:not(.in).right .modal-dialog {
    transform: translate3d(25%, 0, 0);
}
</style>
@endsection
