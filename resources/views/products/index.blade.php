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
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
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
                        <tbody> 
                            @foreach($products as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->location->name ?? '-' }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                                <td>{{ number_format($product->price,2) }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    @if($product->alert_stock >= $product->quantity)
                                        <span class="badge badge-danger">Low Stock > {{ $product->alert_stock }}</span>
                                    @else
                                        <span class="badge badge-success">{{ $product->alert_stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editproduct{{ $product->id }}">
                                            <i class="fa fa-edit"></i>Edit
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteproduct{{ $product->id }}">
                                            <i class="fa fa-trash"></i>Del
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit Product Modal --}}
                            <div class="modal right fade" id="editproduct{{ $product->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 class="modal-title">Edit Product</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  </div>
                                  <div class="modal-body">
                                      <form action="{{ route('products.update',$product->id) }}" method="post">
                                          @csrf
                                          @method('put')
                                          <div class="form-group">
                                              <label>Product Name</label>
                                              <input type="text" name="product_name" class="form-control" value="{{ $product->product_name }}" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Brand</label>
                                              <select name="brand_id" class="form-control" required>
                                                  @foreach($brands as $brand)
                                                      <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                          {{ $brand->name }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="form-group">
                                              <label>Category</label>
                                              <select name="category_id" class="form-control" required>
                                                  @foreach($categories as $category)
                                                      <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
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
                                                      <option value="{{ $location->id }}" {{ $product->location_id == $location->id ? 'selected' : '' }}>
                                                          {{ $location->name }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="form-group">
                                              <label>Price</label>
                                              <input type="number" name="price" value="{{ $product->price }}" class="form-control" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Quantity</label>
                                              <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Alert Stock</label>
                                              <input type="number" name="alert_stock" value="{{ $product->alert_stock }}" class="form-control" required>
                                          </div>
                                          <div class="form-group">
                                              <label>Description</label>
                                              <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                                          </div>
                                          <div class="modal-footer">
                                              <button class="btn btn-primary btn-block">Update Product</button>
                                          </div>
                                      </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                            {{-- Delete Product Modal --}}
                            <div class="modal right fade" id="deleteproduct{{ $product->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 class="modal-title">Delete Product</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  </div>
                                  <div class="modal-body">
                                    <form action="{{ route('products.destroy',$product->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <p>Are you sure you want to delete {{ $product->product_name }}?</p>
                                        <div class="modal-footer">
                                            <button class="btn btn-info" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
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

        <!-- RIGHT SIDE: SEARCH PRODUCT -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header"><h4>Search Product</h4></div>
                <div class="card-body">...</div>
            </div>
        </div>

    </div>

</div>

{{-- Add Product Modal --}}
<div class="modal right fade" id="addproduct" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Product</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <form action="{{ route('products.store') }}" method="POST">
              @csrf
              <div class="form-group">
                  <label>Product Name</label>
                  <input type="text" name="product_name" class="form-control" required>
              </div>
              <div class="form-group">
                  <label>Brand</label>
                  <select name="brand_id" class="form-control" required>
                      <option value="">-- Select Brand --</option>
                      @foreach($brands as $brand)
                          <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group">
                  <label>Category</label>
                  <select name="category_id" class="form-control" required>
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
              <div class="modal-footer">
                  <button class="btn btn-primary btn-block">Save Product</button>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>

{{-- Add Brand Modal --}}
@if(auth()->user()->isAdmin())
<div class="modal right fade" id="addbrand" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Brand</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <form action="{{ route('brands.store') }}" method="POST">
              @csrf
              <div class="form-group">
                  <label>Brand Name</label>
                  <input type="text" name="name" class="form-control" required>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-primary btn-block">Save Brand</button>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Add Category Modal --}}
@if(auth()->user()->isAdmin())
<div class="modal right fade" id="addcategory" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <form action="{{ route('categories.store') }}" method="POST">
              @csrf
              <div class="form-group">
                  <label>Category Name</label>
                  <input type="text" name="name" class="form-control" required>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-primary btn-block">Save Category</button>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>
@endif

<style>
.modal.right .modal-dialog{
    top: 0;
    right: 0;
    margin-right: 19vh;
}
.modal.fade:not(.in).right .modal-dialog{
    transform: translate3d(25%,0,0);
}
</style>

@endsection
