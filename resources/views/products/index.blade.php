 @extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row">

        <!-- LEFT SIDE: productS TABLE -->
        <div class="col-md-9">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center"> 
                    <h4 style="float: left">Add Products</h4>

                    <span>
                        <i class="fa fa-products"></i> products
                    </span>

                    <a href="#" style="float: right" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#addproduct">
                        <i class="fa fa-plus"></i> Add New Products
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product Name</th>
                                <th>Category</th> 
                                <th>Brand</th> 
                                <th>Price</th>
                                <th>Quantity</th>     
                                <th>Alert stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($products as $key => $product)
                            <tr>

                                <td>{{ $key+1 }}</td>
                                <td>{{ $product->product_name}}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->brand->name ?? 'No Brand' }}</td>
                                <td>{{ number_format($product->price,2) }}</td>
                                <td>{{ $product->quantity}}</td>
                                <td>
                                    @if ($product->alert_stock >= $product->quantity) <span class="badge badge-danger">
                                    Low Stock > {{ $product->alert_stock}}</span>
                                    @else 
                                    <span class="badge badge-success"> {{ $product->alert_stock }}</span>
                                @endif
                            </td>

                               
                                <td>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-info btn-sm"data-toggle="modal" 
                                        data-target="#editproduct{{ $product->id }}"><i class="fa fa-edit">
                                            </i>Edit</a>
                                            <a href="#"data-toggle="modal" 
                                        data-target="#deleteproduct{{ $product->id }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>Del</a>
                                    </div>
                                </td>
                
                            </tr>

                            {{--Modal of edit product Details--}}
 <div class="modal right fade" id="editproduct{{ $product->id }}"  data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="staticBackdropLabel">Edit product</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
       {{ $product->id }}
      </div>
      <div class="modal-body">
            <form action="{{ route('products.update',$product->id) }}" method="post">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="">Product Name</label>
                <input type="text" name="product_name" id="" value="{{ $product->product_name }}" class="form-control">
            </div>
             <div class="form-group">
                <label for="">Brand</label>
                <input type="text" name="brand" id="" value="{{ $product->brand }}" class="form-control">
            </div>
            
             <div class="form-group">
                <label for="">Price</label>
                <input type="number" name="price" id="" value="{{ $product->price }}" class="form-control">
            </div>
             <div class="form-group">
                <label for="">Quantity</label>
                <input type="number" name="quantity" id="" value="{{ $product->quantity }}"class="form-control">
            </div>
             <div class="form-group">
                <label for="">Alert Stock</label>
                <input type="number" name="alert_stock" id="" value="{{ $product->alert_stock }}" class="form-control">
            </div>
             <div class="form-group">
                <label for="">description</label>
                <textarea name="description" id="" cols="30" rows="2" class="form-control">{{ $product->description }}</textarea>
                
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-primary btn-block">Update Product</button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>


                                   {{--Modal of edit product Details--}}
 <div class="modal right fade" id="deleteproduct{{ $product->id }}"  data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog">
     <div class="modal-content">
       <div class="modal-header">
        <h4 class="modal-title" id="staticBackdropLabel">Delete product</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          {{ $product->id }}
           </div>
            <div class="modal-body">
             <form action="{{ route('products.destroy', $product->id) }}" method="post">
            @csrf
            @method('delete')

            <p>Are you sure you want to delete this {{ $product->product_name }} ?</p>
            
            <div class="modal-footer">
                <button class="btn btn-info" data-dismiss = "modal">cancel</button>
                <button  type="submit" class="btn btn-danger">Delete</button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>
                            @endforeach
                            {{ $products->links() }}
                        </tbody>
                        
                    </table>
                </div>

            </div>
        </div>

        <!-- RIGHT SIDE: SEARCH product -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>Search product</h4>
                </div>
                <div class="card-body">
                    ......................
                </div>
            </div>
        </div>

    </div>

</div>

  {{-- Modal of adding new product --}}
<div class="modal right fade" id="addproduct" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="staticBackdropLabel">Add product</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{ route('products.store')}}" method="post">
          @csrf
          
          <div class="form-group">
            <label for="">Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
          </div>

            {{--add category--}}        
  <div class="form-group">
    <label for="">Category</label>
      <div class="input-group">
        <select name="category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <div class="input-group-append">
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addCategoryModal">
                + Add Category
            </button>
        </div>
      </div>
    </div>

{{-- add brand--}}
          <div class="form-group">
            <label for="">Brand</label>
            <div class="input-group">
              <select name="brand_id" id="brand" class="form-control" required>
                <option value="">-- Select Brand --</option>
                @foreach($brands as $brand)
                  <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
              </select>
              <div class="input-group-append">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addBrandModal">
                  + Add Brand
                </button>
              </div>
            </div>
          </div>



          <div class="form-group">
            <label for="">Price</label>
            <input type="number" name="price" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="">Alert Stock</label>
            <input type="number" name="alert_stock" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="">Description</label>
            <textarea name="description" cols="30" rows="2" class="form-control"></textarea>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-block">Save Product</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


     {{-- Add Category Modal (outside Add Product modal) --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoryLabel">Add Category</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="{{ route('categories.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" name="name" id="category_name" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Add Category</button>
        </form>
      </div>
    </div>
  </div>
</div>


{{-- Add Brand Modal (outside Add Product modal) --}}
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBrandLabel">Add Brand</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="{{ route('brands.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="brand_name">Brand Name</label>
            <input type="text" name="name" id="brand_name" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Add Brand</button>
        </form>
      </div>
    </div>
  </div>
</div>


<style>
  .modal.right .modal-dialog {
      top: 0;
      right: 0;
      margin-right: 19vh;
  }

  .modal.fade:not(.in).right .modal-dialog {
      -webkit-transform: translate3d(25%,0,0);
      transform: translate3d(25%,0,0);
  }
</style>
@endsection
