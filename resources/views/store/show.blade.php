@extends('layouts.store')

@section('title', $product->product_name)

@section('content')
<div class="container py-5">

    <div class="row align-items-center g-5">

        <!-- IMAGE -->
        <div class="col-lg-6 text-center">
            <div class="p-3 bg-light rounded-4 shadow-sm">
                <img src="{{ asset('images/products/'.$product->image) }}"
                     class="img-fluid rounded-4"
                     style="max-height:450px; object-fit:cover;">
            </div>
        </div>

        <!-- DETAILS -->
        <div class="col-lg-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">

                    <h2 class="fw-bold mb-3">{{ $product->product_name }}</h2>

                    <h3 class="text-primary fw-bold mb-3">
                        Ksh <span id="productTotal">{{ number_format($product->price, 2) }}</span>
                    </h3>

                    <p class="text-muted mb-4">{{ $product->description }}</p>

                    @php
                        $cart = session()->get('cart', []);
                        $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
                    @endphp

                    <form id="addToCartForm">
                        @csrf
                        <input type="hidden" id="unitPrice" value="{{ $product->price }}">
                        <input type="hidden" name="quantity" id="quantityInput" value="{{ $currentQty > 0 ? $currentQty : 1 }}">
                        <input type="hidden" id="productId" value="{{ $product->id }}">

                        <button type="button"
                                class="btn btn-dark btn-lg w-100 rounded-pill"
                                id="addToCartBtn"
                                @if($currentQty > 0) style="display:none;" @endif>
                            Add to Cart
                        </button>

                        <div class="d-flex justify-content-center align-items-center mt-3 gap-3 @if($currentQty == 0) d-none @endif"
                             id="qtyControls">

                            <button type="button" class="btn btn-outline-dark rounded-circle px-3" id="minusBtn">−</button>

                            <span class="fw-bold fs-5" id="qtyDisplay">
                                {{ $currentQty > 0 ? $currentQty : 1 }}
                            </span>

                            <button type="button" class="btn btn-outline-dark rounded-circle px-3" id="plusBtn">+</button>
                        </div>
                    </form>

                    <a href="{{ route('store.index') }}"
                       class="btn btn-link w-100 mt-4 text-decoration-none">
                        ← Go to Store
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ YOUR ORIGINAL SCRIPT (UNCHANGED) -->
<script>
let quantity = {{ $currentQty > 0 ? $currentQty : 1 }};

const unitPrice = parseFloat(document.getElementById('unitPrice').value);
const qtyDisplay = document.getElementById('qtyDisplay');
const qtyInput = document.getElementById('quantityInput');
const productId = document.getElementById('productId').value;

const addToCartBtn = document.getElementById('addToCartBtn');
const qtyControls = document.getElementById('qtyControls');

const plusBtn = document.getElementById('plusBtn');
const minusBtn = document.getElementById('minusBtn');

const cartCountEl = document.getElementById('cartCount');
const cartNavTotalEl = document.getElementById('cartTotal');

function money(v){
    return v.toLocaleString(undefined,{minimumFractionDigits:2});
}

function refresh(){
    qtyDisplay.innerText = quantity;
    qtyInput.value = quantity;

    if(cartCountEl) cartCountEl.innerText = quantity;
    if(cartNavTotalEl) cartNavTotalEl.innerText = money(quantity * unitPrice);
}

function updateCartServer() {
    fetch("{{ url('/cart/add') }}/" + productId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'quantity=' + quantity
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            if(cartCountEl) cartCountEl.innerText = data.cartCount;
            if(cartNavTotalEl) cartNavTotalEl.innerText = money(data.cartTotal);
        }
    })
    .catch(error => console.log(error));
}

addToCartBtn.addEventListener('click', function () {
    quantity = 1;
    updateCartServer();
    refresh();

    addToCartBtn.style.display = 'none';
    qtyControls.classList.remove('d-none');
});

plusBtn.addEventListener('click', function () {
    quantity++;
    refresh();
    updateCartServer();
});

minusBtn.addEventListener('click', function () {
    if (quantity > 1) {
        quantity--;
        refresh();
        updateCartServer();
    }
});
</script>

@endsection
