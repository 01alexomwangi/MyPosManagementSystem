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

                    <!-- STATIC Product Price -->
                    <h4 class="text-primary fw-bold">
                        Ksh <span id="productTotal">{{ number_format($product->price, 2) }}</span>
                    </h4>

                    <p class="text-muted">{{ $product->description }}</p>

                    <form id="addToCartForm">
                        @csrf
                        <input type="hidden" id="unitPrice" value="{{ $product->price }}">
                        <input type="hidden" name="quantity" id="quantityInput" value="1">
                        <input type="hidden" id="productId" value="{{ $product->id }}">

                        
                       <!-- ADD TO CART BUTTON (initially visible) -->
  <button type="button" class="btn btn-dark w-100" id="addToCartBtn">
    Add to Cart
  </button>

  <!-- QUANTITY CONTROLS (hidden initially) -->
  <div class="d-flex justify-content-center align-items-center mt-2 d-none"
     id="qtyControls">

    <button type="button" class="btn btn-dark btn-sm" id="minusBtn">−</button>

    <span class="fw-bold text-black px-3" id="qtyDisplay">1</span>

    <button type="button" class="btn btn-dark btn-sm" id="plusBtn">+</button>
 </div>



                    </form>

                    <a href="{{ url('/') }}" class="btn btn-link w-100 mt-3">← Back</a>

                </div>
            </div>
        </div>
    </div>
</div>

<script>

let quantity = 1;

// Elements
const unitPrice = parseFloat(document.getElementById('unitPrice').value);
const qtyDisplay = document.getElementById('qtyDisplay');
const qtyInput = document.getElementById('quantityInput');
const productId = document.getElementById('productId').value;

const addToCartBtn = document.getElementById('addToCartBtn');
const qtyControls = document.getElementById('qtyControls');

const plusBtn = document.getElementById('plusBtn');
const minusBtn = document.getElementById('minusBtn');

// Navbar
const cartCountEl = document.getElementById('cartCount');
const cartNavTotalEl = document.getElementById('cartTotal');

// Money formatter
function money(v){
    return v.toLocaleString(undefined,{minimumFractionDigits:2});
}

// Refresh UI
function refresh(){
    qtyDisplay.innerText = quantity;
    qtyInput.value = quantity;

    if(cartCountEl) cartCountEl.innerText = quantity;
    if(cartNavTotalEl) cartNavTotalEl.innerText = money(quantity * unitPrice);
}

// AJAX update
function updateCartServer() {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity })
    });
}

// ADD TO CART (first click)
addToCartBtn.addEventListener('click', function () {
    quantity = 1;
    updateCartServer();
    refresh();

    addToCartBtn.classList.add('d-none');   // hide button
    qtyControls.classList.remove('d-none'); // show +/-
});

// PLUS
plusBtn.addEventListener('click', function () {
    quantity++;
    refresh();
    updateCartServer();
});

// MINUS
minusBtn.addEventListener('click', function () {
    if (quantity > 1) {
        quantity--;
        refresh();
        updateCartServer();
    }
});
</script>

@endsection
