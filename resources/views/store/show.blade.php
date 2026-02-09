@extends('layouts.store')

@section('title', $product->product_name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">

        <!-- IMAGE -->
        <div class="col-md-5 mb-4">
            <img src="{{ asset('images/products/'.$product->image) }}"
                 class="img-fluid rounded shadow-sm">
        </div>

        <!-- DETAILS -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h3 class="fw-bold">{{ $product->product_name }}</h3>

                    <h4 class="text-primary fw-bold">
                        Ksh <span id="productTotal">{{ number_format($product->price, 2) }}</span>
                    </h4>

                    <p class="text-muted">{{ $product->description }}</p>

                   <form method="POST" action="{{ url('/cart/add/'.$product->id) }}" id="addToCartForm">
    @csrf

    <input type="hidden" id="unitPrice" value="{{ $product->price }}">
    <input type="hidden" name="quantity" id="quantityInput" value="0">

    <!-- ADD TO CART BUTTON -->
    <button type="submit"
        class="btn btn-success btn-lg w-100 d-flex justify-content-between align-items-center px-3">

        <span class="btn btn-light btn-sm" id="minusBtn">−</span>

        <span class="fw-bold text-white">
            <span id="qtyDisplay">1</span> × Add to Cart
        </span>

        <span class="btn btn-light btn-sm" id="plusBtn">+</span>

    </button>
</form>


                    <a href="{{ url('/') }}" class="btn btn-link w-100 mt-3">← Back</a>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
let quantity = 0;

const unitPrice   = parseFloat(document.getElementById('unitPrice').value);
const qtyDisplay  = document.getElementById('qtyDisplay');
const qtyInput    = document.getElementById('quantityInput');
const productTotal = document.getElementById('productTotal');

function money(v){
    return v.toLocaleString(undefined,{minimumFractionDigits:2});
}

function refresh(){
    qtyDisplay.innerText = quantity;
    qtyInput.value = quantity;
    productTotal.innerText = money(quantity * unitPrice);
}

document.getElementById('plusBtn').onclick = (e) => {
    e.preventDefault();
    quantity++;
    refresh();
};

document.getElementById('minusBtn').onclick = (e) => {
    e.preventDefault();
    if(quantity > 1){
        quantity--;
        refresh();
    }
};

refresh();
</script>

@endsection
