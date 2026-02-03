@extends('layouts.store')

@section('title', 'Our Products')

@section('content')
<div class="container py-5">

    <!-- PAGE TITLE -->
    <div class="row mb-4">
        <div class="col text-center">
            <h2 class="fw-bold">Our Products</h2>
            <p class="text-muted">Browse our latest items</p>
        </div>
    </div>

    <!-- PRODUCTS GRID -->
    <div class="row">
        @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0">

                    <!-- IMAGE PLACEHOLDER -->
                    <div class="bg-secondary text-white text-center py-5">
                        <small>Product Image</small>
                    </div>

                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-2">{{ $product->name }}</h6>
                        <p class="text-primary fw-bold mb-3">Ksh {{ number_format($product->price, 2) }}</p>
                        <a href="{{ url('/product/'.$product->id) }}" class="btn btn-outline-primary btn-sm w-100">
                            View Product
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- PAGINATION -->
    <div class="row mt-4">
        <div class="col d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
