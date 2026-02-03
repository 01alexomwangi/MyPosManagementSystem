@extends('layouts.store')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">

        <!-- PRODUCT IMAGE -->
        <div class="col-md-5 mb-4">
            <div class="bg-secondary text-white text-center py-5 rounded shadow-sm">
                <h5>Product Image</h5>
            </div>
        </div>

        <!-- PRODUCT DETAILS -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold mb-3">{{ $product->name }}</h3>
                    <h4 class="text-primary fw-bold mb-3">Ksh {{ number_format($product->price, 2) }}</h4>
                    <p class="text-muted mb-4">{{ $product->description }}</p>

                    <form method="POST" action="{{ url('/cart/add/'.$product->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg w-100">Add to Cart</button>
                    </form>

                    <a href="{{ url('/') }}" class="btn btn-link mt-3 w-100">← Back to shop</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
