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
                    <div class="row align-items-center py-3 border-bottom" data-key="{{ $key }}">
                        <!-- PRODUCT NAME -->
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-1">{{ $item['name'] }}</h6>
                        </div>

                        <!-- QUANTITY -->
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm minusBtn">−</button>
                                <span class="fw-bold qtyDisplay">{{ $item['quantity'] }}</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm plusBtn">+</button>
                            </div>
                        </div>

                        <!-- PRICE -->
                        <div class="col-md-2">
                            Ksh <span class="price">{{ number_format($item['price'], 2) }}</span>
                        </div>

                        <!-- ITEM TOTAL -->
                        <div class="col-md-2 fw-bold text-primary">
                            Ksh <span class="itemTotal">{{ number_format($item['total_amount'], 2) }}</span>
                        </div>

                        <!-- REMOVE -->
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger removeBtn">
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
                        <span>Ksh <span id="cartSubtotalDisplay">{{ number_format($cartTotal, 2) }}</span></span>
                    </div>

                    <hr>

                    <!-- DELIVERY METHOD -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="fw-semibold mb-3">Delivery Method</label>

                            <div class="form-check mb-2">
                                <input class="form-check-input deliveryOption" type="radio" name="delivery_method" value="pickup" checked>
                                <label class="form-check-label">Pickup (Free)</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input deliveryOption" type="radio" name="delivery_method" value="rider">
                                <label class="form-check-label">Rider Delivery (Calculated after address)</label>
                            </div>
                        </div>
                    </div>

                    <!-- DELIVERY FEE SUMMARY -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Fee</span>
                                <span>Ksh <span id="deliveryFeeDisplay">0.00</span></span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>Ksh <span id="orderTotalDisplay">{{ number_format($cartTotal, 2) }}</span></strong>
                            </div>
                        </div>
                    </div>

                    <!-- RIDER DELIVERY FIELDS -->
                    <div id="riderFields" class="card mb-4" style="display:none;">
                        <div class="card-body">
                            <h5 class="mb-3">Delivery Details</h5>

                            <div class="mb-3">
                                <label class="form-label">Dropoff Address</label>
                                <input type="text" id="dropoff_address" name="dropoff_address" class="form-control" placeholder="Enter delivery address">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Dropoff Latitude</label>
                                    <input type="text" id="dropoff_latitude" name="dropoff_latitude" class="form-control" placeholder="-1.2921">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Dropoff Longitude</label>
                                    <input type="text" id="dropoff_longitude" name="dropoff_longitude" class="form-control" placeholder="36.8219">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Recipient Name</label>
                                <input type="text" id="recipient_name" name="recipient_name" class="form-control" placeholder="Full name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Recipient Mobile</label>
                                <input type="text" id="recipient_mobile" name="recipient_mobile" class="form-control" placeholder="2547XXXXXXXX">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Delivery Notes (Optional)</label>
                                <textarea id="delivery_notes" name="delivery_notes" class="form-control" rows="3" placeholder="Any instructions for rider"></textarea>
                            </div>

                            <button type="button" id="calculateDelivery" class="btn btn-primary w-100">Calculate Delivery</button>
                        </div>
                    </div>

                    <hr>

                    <!-- CHECKOUT -->
                    <form action="{{ route('customer.cart.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="delivery_method" id="deliveryMethodInput" value="pickup">
                        <input type="hidden" name="delivery_fee" id="deliveryFeeInput" value="0">
                        <button class="btn btn-dark w-100 rounded-pill mb-2">Proceed to Checkout</button>
                    </form>

                    <!-- CLEAR CART -->
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger w-100 rounded-pill">Clear Cart</button>
                    </form>

                    <a href="{{ route('store.index') }}" class="btn btn-link w-100 mt-4 text-decoration-none">← Go to Store</a>

                </div>
            </div>
        </div>

        @else
        <!-- EMPTY CART -->
        <div class="col-12 text-center py-5">
            <h4 class="fw-bold mb-3">🛒 Your cart is empty</h4>
            <a href="{{ url('/') }}" class="btn btn-dark rounded-pill px-4">Continue Shopping</a>
        </div>
        @endif

    </div>
</div>

<!-- ========================= -->
<!-- 🔥 FULL SCRIPT -->
<!-- ========================= -->
<script>
document.addEventListener("DOMContentLoaded", function() {

    // ==========================
    // CART ROW LOGIC (+, -, REMOVE)
    // ==========================
    const cartRows = document.querySelectorAll('[data-key]');
    const cartSubtotalEl = document.getElementById('cartSubtotalDisplay');
    const orderTotalEl = document.getElementById('orderTotalDisplay');
    const deliveryFeeDisplay = document.getElementById('deliveryFeeDisplay');
    const deliveryOptions = document.querySelectorAll('.deliveryOption');
    const deliveryMethodInput = document.getElementById('deliveryMethodInput');
    const deliveryFeeInput = document.getElementById('deliveryFeeInput');
    const cartCountEl = document.getElementById('cartCount');
    const cartNavTotalEl = document.getElementById('cartTotal');

    function formatMoney(value) {
        return Number(value).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function updateFinalTotal(subtotal, fee=0) {
        deliveryFeeDisplay.innerText = formatMoney(fee);
        orderTotalEl.innerText = formatMoney(subtotal + fee);
        deliveryFeeInput.value = fee;
    }

    // Show/hide rider fields
    const riderFields = document.getElementById('riderFields');
    deliveryOptions.forEach(option => {
        option.addEventListener('change', function() {
            if(this.value === 'rider') {
                riderFields.style.display = 'block';
            } else {
                riderFields.style.display = 'none';
                updateFinalTotal(parseFloat(cartSubtotalEl.innerText.replace(/,/g, '')), 0);
            }
            deliveryMethodInput.value = this.value;
        });
    });

    // Cart row buttons (+, -, remove)
    cartRows.forEach(row => {
        const plusBtn = row.querySelector('.plusBtn');
        const minusBtn = row.querySelector('.minusBtn');
        const qtyDisplay = row.querySelector('.qtyDisplay');
        const priceEl = row.querySelector('.price');
        const itemTotalEl = row.querySelector('.itemTotal');
        const removeBtn = row.querySelector('.removeBtn');

        let quantity = parseInt(qtyDisplay.innerText);
        const price = parseFloat(priceEl.innerText.replace(/,/g,''));

        function updateItemTotal() {
            fetch("{{ route('cart.update', '') }}/" + row.dataset.key, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type':'application/x-www-form-urlencoded'},
                body: 'quantity=' + quantity
            })
            .then(res => res.json())
            .then(data => {
                itemTotalEl.innerText = formatMoney(quantity*price);
                cartSubtotalEl.innerText = formatMoney(data.cartTotal);
                updateFinalTotal(data.cartTotal, parseFloat(deliveryFeeDisplay.innerText.replace(/,/g,'')));
                if(cartCountEl) cartCountEl.innerText = data.cartCount;
                if(cartNavTotalEl) cartNavTotalEl.innerText = formatMoney(data.cartTotal);
            });
        }

        plusBtn.addEventListener('click', ()=> { quantity++; qtyDisplay.innerText = quantity; updateItemTotal(); });
        minusBtn.addEventListener('click', ()=> { if(quantity>1){ quantity--; qtyDisplay.innerText = quantity; updateItemTotal(); } });
        removeBtn.addEventListener('click', ()=>{
            fetch("{{ route('cart.remove','') }}/"+row.dataset.key, {
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
            })
            .then(res=>res.json())
            .then(data=>{
                row.remove();
                cartSubtotalEl.innerText = formatMoney(data.cartTotal);
                updateFinalTotal(data.cartTotal, parseFloat(deliveryFeeDisplay.innerText.replace(/,/g,'')));
                if(cartCountEl) cartCountEl.innerText = data.cartCount;
                if(cartNavTotalEl) cartNavTotalEl.innerText = formatMoney(data.cartTotal);
                if(data.cartCount === 0) location.reload();
            });
        });
    });

    // ==========================
    // CALCULATE DELIVERY BUTTON AJAX
    // ==========================
    document.getElementById('calculateDelivery').addEventListener('click', function() {
    const dropoffLat = document.getElementById('dropoff_latitude').value;
    const dropoffLng = document.getElementById('dropoff_longitude').value;
    const dropoffAddr = document.getElementById('dropoff_address').value;
    const recipientName = document.getElementById('recipient_name').value;
    const recipientMobile = document.getElementById('recipient_mobile').value;

    // ✅ Add this to see what's being sent
    console.log('Sending:', { dropoffLat, dropoffLng, dropoffAddr, recipientName, recipientMobile });

    fetch("{{ route('delivery.estimate') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            dropoff_latitude: dropoffLat,
            dropoff_longitude: dropoffLng,
            dropoff_address: dropoffAddr,
            recipient_name: recipientName,
            recipient_mobile: recipientMobile
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Response:', data); // ✅ See full response
        if(data.success){
            updateFinalTotal(parseFloat(cartSubtotalEl.innerText.replace(/,/g,'')), parseFloat(data.delivery_fee));
            deliveryFeeInput.value = data.delivery_fee;
        } else {
            alert(data.message || "Delivery estimation failed");
        }
    })
    .catch(err => {
        console.error('Fetch error:', err); // ✅ Catch network/JS errors
        alert('Request failed: ' + err.message);
    });
});

document.querySelector('form[action*="checkout"]').addEventListener('submit', function(e) {
    const deliveryMethod = document.getElementById('deliveryMethodInput').value;
    const deliveryFee = parseFloat(document.getElementById('deliveryFeeInput').value);

    if (deliveryMethod === 'rider' && deliveryFee <= 0) {
        e.preventDefault();
        alert('Please calculate delivery fee first before checking out.');
        return false;
    }
});

});
</script>

@endsection