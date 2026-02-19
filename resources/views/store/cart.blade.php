@extends('layouts.store')

@section('title', 'Your Cart')

@section('content')
<div class="container py-5">

    @php
        $cart = session()->get('cart', []);
        $cartTotal = array_sum(array_column($cart, 'total_amount'));
        $cartQty = array_sum(array_column($cart, 'quantity'));
    @endphp

    <div class="row g-4">

        @if(count($cart) > 0)

        <!-- LEFT SIDE - CART ITEMS -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-4">
                        Cart ({{ $cartQty }} items)
                    </h5>

                    @foreach($cart as $key => $item)

                    <div class="row align-items-center py-3 border-bottom"
                         data-key="{{ $key }}">

                        <!-- PRODUCT NAME -->
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-1">
                                {{ $item['name'] }}
                            </h6>
                        </div>

                        <!-- QUANTITY -->
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm minusBtn">−</button>

                                <span class="fw-bold qtyDisplay">
                                    {{ $item['quantity'] }}
                                </span>

                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm plusBtn">+</button>
                            </div>
                        </div>

                        <!-- PRICE -->
                        <div class="col-md-2">
                            Ksh <span class="price">
                                {{ number_format($item['price'], 2) }}
                            </span>
                        </div>

                        <!-- ITEM TOTAL -->
                        <div class="col-md-2 fw-bold text-primary">
                            Ksh <span class="itemTotal">
                                {{ number_format($item['total_amount'], 2) }}
                            </span>
                        </div>

                        <!-- REMOVE -->
                        <div class="col-md-1 text-end">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger removeBtn">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                    </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- RIGHT SIDE - ORDER SUMMARY -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:100px;">
                <div class="card-body p-4">

                    <h6 class="fw-bold mb-3">Order Summary</h6>

                    <!-- SUBTOTAL -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>
                            Ksh <span id="cartSubtotalDisplay">
                                {{ number_format($cartTotal, 2) }}
                            </span>
                        </span>
                    </div>

                    <hr>

                    <!-- DELIVERY METHOD -->
                    <div class="mb-3">
                        <label class="fw-semibold mb-2">Delivery Method</label>

                        <div class="form-check">
                            <input class="form-check-input deliveryOption"
                                   type="radio"
                                   name="delivery_method"
                                   value="pickup"
                                   data-fee="0"
                                   checked>
                            <label class="form-check-label">
                                Pickup (Free)
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input deliveryOption"
                                   type="radio"
                                   name="delivery_method"
                                   value="rider"
                                   data-fee="200">
                            <label class="form-check-label">
                                Rider Delivery (Ksh 200)
                            </label>
                        </div>
                    </div>

                    <!-- DELIVERY FEE -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee</span>
                        <span>
                            Ksh <span id="deliveryFeeDisplay">0.00</span>
                        </span>
                    </div>

                    <hr>

                    <!-- TOTAL -->
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Total</span>
                        <span class="text-primary">
                            Ksh <span id="cartTotalDisplay">
                                {{ number_format($cartTotal, 2) }}
                            </span>
                        </span>
                    </div>

                    <!-- CHECKOUT -->
                    <form action="{{ route('customer.cart.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="delivery_method"
                               id="deliveryMethodInput" value="pickup">

                        <button class="btn btn-dark w-100 rounded-pill mb-2">
                            Proceed to Checkout
                        </button>
                    </form>

                    <!-- CLEAR CART -->
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger w-100 rounded-pill">
                            Clear Cart
                        </button>
                    </form>

                    <a href="{{ route('store.index') }}"
                       class="btn btn-link w-100 mt-4 text-decoration-none">
                        ← Go to Store
                    </a>

                </div>
            </div>
        </div>

        @else

        <!-- EMPTY CART -->
        <div class="col-12 text-center py-5">
            <h4 class="fw-bold mb-3">🛒 Your cart is empty</h4>
            <a href="{{ url('/') }}" class="btn btn-dark rounded-pill px-4">
                Continue Shopping
            </a>
        </div>

        @endif

    </div>
</div>

<!-- ========================= -->
<!-- 🔥 FULL SCRIPT -->
<!-- ========================= -->
<script>
document.addEventListener("DOMContentLoaded", function() {

    const cartRows = document.querySelectorAll('[data-key]');
    const cartSubtotalEl = document.getElementById('cartSubtotalDisplay');
    const cartTotalEl = document.getElementById('cartTotalDisplay');
    const deliveryOptions = document.querySelectorAll('.deliveryOption');
    const deliveryFeeDisplay = document.getElementById('deliveryFeeDisplay');
    const deliveryMethodInput = document.getElementById('deliveryMethodInput');
    const cartCountEl = document.getElementById('cartCount');
    const cartNavTotalEl = document.getElementById('cartTotal');

    function formatMoney(value) {
        return Number(value).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateFinalTotal(subtotal) {
        let selected = document.querySelector('.deliveryOption:checked');
        let fee = parseFloat(selected.dataset.fee);
        let finalTotal = subtotal + fee;

        deliveryFeeDisplay.innerText = formatMoney(fee);
        cartTotalEl.innerText = formatMoney(finalTotal);
        deliveryMethodInput.value = selected.value;
    }

    deliveryOptions.forEach(option => {
        option.addEventListener('change', function(){
            let subtotal = parseFloat(cartSubtotalEl.innerText.replace(/,/g, ''));
            updateFinalTotal(subtotal);
        });
    });

    cartRows.forEach(row => {

        const plusBtn = row.querySelector('.plusBtn');
        const minusBtn = row.querySelector('.minusBtn');
        const qtyDisplay = row.querySelector('.qtyDisplay');
        const priceEl = row.querySelector('.price');
        const itemTotalEl = row.querySelector('.itemTotal');
        const removeBtn = row.querySelector('.removeBtn');

        let quantity = parseInt(qtyDisplay.innerText);
        const price = parseFloat(priceEl.innerText.replace(/,/g, ''));

        function updateItemTotal() {

            fetch("{{ route('cart.update', '') }}/" + row.dataset.key, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'quantity=' + quantity
            })
            .then(res => res.json())
            .then(data => {

                itemTotalEl.innerText = formatMoney(quantity * price);

                cartSubtotalEl.innerText = formatMoney(data.cartTotal);

                updateFinalTotal(data.cartTotal);

                if(cartCountEl) cartCountEl.innerText = data.cartCount;
                if(cartNavTotalEl) cartNavTotalEl.innerText = formatMoney(data.cartTotal);
            });
        }

        plusBtn.addEventListener('click', function(){
            quantity++;
            qtyDisplay.innerText = quantity;
            updateItemTotal();
        });

        minusBtn.addEventListener('click', function(){
            if(quantity > 1){
                quantity--;
                qtyDisplay.innerText = quantity;
                updateItemTotal();
            }
        });

        removeBtn.addEventListener('click', function(){

            fetch("{{ route('cart.remove', '') }}/" + row.dataset.key, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {

                row.remove();

                cartSubtotalEl.innerText = formatMoney(data.cartTotal);

                updateFinalTotal(data.cartTotal);

                if(cartCountEl) cartCountEl.innerText = data.cartCount;
                if(cartNavTotalEl) cartNavTotalEl.innerText = formatMoney(data.cartTotal);

                if(data.cartCount === 0) {
                    location.reload();
                }
            });
        });

    });

});
</script>

@endsection
