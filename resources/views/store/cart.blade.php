@extends('layouts.store')

@section('title', 'Your Cart')

@section('content')
<div class="container py-4">
    <h3>Your Cart</h3>

    @php
        $cart = session()->get('cart', []);
        $cartTotal = array_sum(array_column($cart, 'total_amount'));
        $cartQty = array_sum(array_column($cart, 'quantity'));
    @endphp

    @if(count($cart) > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="width: 150px;">Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $key => $item)
                <tr data-key="{{ $key }}">
                    <td>{{ $item['name'] }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary minusBtn">−</button>
                            <span class="fw-bold fs-6 qtyDisplay">{{ $item['quantity'] }}</span>
                            <button type="button" class="btn btn-outline-secondary plusBtn">+</button>
                        </div>
                    </td>
                    <td>Ksh <span class="price">{{ number_format($item['price'], 2) }}</span></td>
                    <td>Ksh <span class="itemTotal">{{ number_format($item['total_amount'], 2) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <strong>Total: Ksh <span id="cartTotalDisplay">{{ number_format($cartTotal, 2) }}</span></strong>

            <div class="d-flex gap-2">
                <form action="{{ route('customer.cart.checkout') }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Proceed to Checkout</button>
                </form>

                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger">Clear Cart</button>
                </form>
            </div>
        </div>
    @else
        <p>Your cart is empty. <a href="{{ url('/') }}">Go back to shop</a></p>
    @endif
</div>

{{-- REAL-TIME QUANTITY & NAVBAR UPDATE --}}
<script>
    const cartRows = document.querySelectorAll('tr[data-key]');
    const cartTotalEl = document.getElementById('cartTotalDisplay');
    const cartCountEl = document.getElementById('cartCount');
    const cartNavTotalEl = document.getElementById('cartTotal');

    function formatMoney(value) {
        return value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    cartRows.forEach(row => {
        const plusBtn = row.querySelector('.plusBtn');
        const minusBtn = row.querySelector('.minusBtn');
        const qtyDisplay = row.querySelector('.qtyDisplay');
        const priceEl = row.querySelector('.price');
        const itemTotalEl = row.querySelector('.itemTotal');

        let quantity = parseInt(qtyDisplay.innerText);
        const price = parseFloat(priceEl.innerText.replace(/,/g, ''));

        function updateItemTotal() {
            const total = quantity * price;
            itemTotalEl.innerText = formatMoney(total);
            updateCartTotal();
        }

        function updateCartTotal() {
            let total = 0;
            let totalQty = 0;
            document.querySelectorAll('tr[data-key]').forEach(r => {
                const q = parseInt(r.querySelector('.qtyDisplay').innerText);
                const p = parseFloat(r.querySelector('.price').innerText.replace(/,/g,''));
                total += q * p;
                totalQty += q;
            });


 
            cartTotalEl.innerText = formatMoney(total);
            if(cartCountEl) cartCountEl.innerText = totalQty;
            if(cartNavTotalEl) cartNavTotalEl.innerText = formatMoney(total);
        }

        plusBtn.addEventListener('click', () => {
            quantity++;
            qtyDisplay.innerText = quantity;
            updateItemTotal();
        });

        minusBtn.addEventListener('click', () => {
            if(quantity > 1){
                quantity--;
                qtyDisplay.innerText = quantity;
                updateItemTotal();
            }
        });
    });
</script>
@endsection
