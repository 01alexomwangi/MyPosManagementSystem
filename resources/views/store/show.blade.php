@extends('layouts.store')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">

        <!-- PRODUCT IMAGE -->
        <div class="col-md-5 mb-4">
            <img src="{{ asset('images/products/'.$product->image) }}"
              class="img-fluid rounded shadow-sm"
                alt="{{ $product->product_name }}">
        </div>

        <!-- PRODUCT DETAILS -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold mb-3">{{ $product->product_name }}</h3>
                    <h4 class="text-primary fw-bold mb-3">Ksh {{ number_format($product->price, 2) }}</h4>
                    <p class="text-muted mb-4">{{ $product->description }}</p>

                    <form method="POST" action="{{ url('/cart/add/'.$product->id) }}">
                        @csrf

                        <div class="input-group mb-3" style="max-width: 200px;">
                            <button type="button" class="btn btn-outline-secondary" id="decrease">-</button>
                            <input type="number" name="quantity" value="1" min="1" class="form-control text-center" id="quantityInput">
                            <button type="button" class="btn btn-outline-secondary" id="increase">+</button>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">Add to Cart</button>
                    </form>

                    <a href="{{ url('/') }}" class="btn btn-link mt-3 w-100">← Back to shop</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const decreaseBtn = document.getElementById('decrease');
    const increaseBtn = document.getElementById('increase');
    const quantityInput = document.getElementById('quantityInput');

    decreaseBtn.addEventListener('click', () => {
        let current = parseInt(quantityInput.value);
        if(current > 1) quantityInput.value = current - 1;
    });

    increaseBtn.addEventListener('click', () => {
        let current = parseInt(quantityInput.value);
        quantityInput.value = current + 1;
    });
</script>
@endsection
