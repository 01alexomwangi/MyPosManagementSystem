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
                        Ksh {{ number_format($product->price, 2) }}
                    </h3>

                    <span class="badge bg-secondary">
                        {{ $product->location->name ?? 'No Branch' }}
                    </span>

                    <p class="text-muted mt-3 mb-4">
                        {{ $product->description }}
                    </p>

                    @php
                        $cart = session()->get('cart', []);
                        $currentQty = isset($cart[$product->id])
                            ? $cart[$product->id]['quantity']
                            : 0;

                        $selectedLocation = session('selected_location');
                    @endphp

                    <!-- FORM -->
                    <form id="addToCartForm">
                        @csrf

                        <input type="hidden" id="unitPrice"
                               value="{{ $product->price }}">

                        <input type="hidden" id="productId"
                               value="{{ $product->id }}">

                        <!-- BUTTON LOGIC -->
                        @if(!$selectedLocation)

                            <button type="button"
                                    class="btn btn-warning w-100"
                                    disabled>
                                Select Location First
                            </button>

                        @elseif($product->location_id != $selectedLocation)

                            <button type="button"
                                    class="btn btn-secondary w-100"
                                    disabled>
                                Not Available In Selected Location
                            </button>

                        @else

                            <!-- ADD BUTTON -->
                            <button type="button"
                                    id="addToCartBtn"
                                    class="btn btn-success w-100"
                                    @if($currentQty > 0) style="display:none;" @endif>
                                Add to Cart
                            </button>

                            <!-- QTY CONTROLS -->
                            <div class="d-flex justify-content-center align-items-center mt-3 gap-3
                                 @if($currentQty == 0) d-none @endif"
                                 id="qtyControls">

                                <button type="button"
                                        class="btn btn-outline-dark rounded-circle px-3"
                                        id="minusBtn">−</button>

                                <span class="fw-bold fs-5"
                                      id="qtyDisplay">
                                    {{ $currentQty > 0 ? $currentQty : 1 }}
                                </span>

                                <button type="button"
                                        class="btn btn-outline-dark rounded-circle px-3"
                                        id="plusBtn">+</button>
                            </div>

                        @endif

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

<script>
document.addEventListener("DOMContentLoaded", function() {

    let quantity = {{ $currentQty > 0 ? $currentQty : 1 }};

    const unitPrice = parseFloat(document.getElementById('unitPrice')?.value || 0);
    const productId = document.getElementById('productId')?.value;

    const addToCartBtn = document.getElementById('addToCartBtn');
    const qtyControls = document.getElementById('qtyControls');
    const qtyDisplay = document.getElementById('qtyDisplay');

    const plusBtn = document.getElementById('plusBtn');
    const minusBtn = document.getElementById('minusBtn');

    const cartCountEl = document.getElementById('cartCount');
    const cartNavTotalEl = document.getElementById('cartTotal');

    function money(v){
        return Number(v).toLocaleString(undefined,{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }

    function refreshUI(){
        if(qtyDisplay) qtyDisplay.innerText = quantity;
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

    if(addToCartBtn){
        addToCartBtn.addEventListener('click', function () {

            quantity = 1;

            updateCartServer();
            refreshUI();

            addToCartBtn.style.display = 'none';
            qtyControls.classList.remove('d-none');
        });
    }

    if(plusBtn){
        plusBtn.addEventListener('click', function () {
            quantity++;
            refreshUI();
            updateCartServer();
        });
    }

    if(minusBtn){
        minusBtn.addEventListener('click', function () {
            if(quantity > 1){
                quantity--;
                refreshUI();
                updateCartServer();
            }
        });
    }

});
</script>

@endsection
