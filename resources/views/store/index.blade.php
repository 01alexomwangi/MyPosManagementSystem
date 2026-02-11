@extends('layouts.store')

@section('title', 'Little Store')

@section('content')
<div class="container py-5">

    <!-- PAGE HEADER -->
    <div class="text-center mb-5">
        <h2 class="fw-bold display-6">Our Products</h2>
        <p class="text-muted">Discover our latest collection</p>
        <hr class="w-25 mx-auto">
    </div>

    <!-- PRODUCTS GRID -->
    <div class="row g-4">
        @foreach($products as $product)
            @php
                $cart = session()->get('cart', []);
                $currentQty = isset($cart[$product->id]) 
                    ? $cart[$product->id]['quantity'] 
                    : 0;
            @endphp

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm product-card position-relative">

                    <!-- STRETCHED LINK (ONLY ONE) -->
                    <a href="{{ route('store.show', $product->id) }}"
                       class="stretched-link"></a>

                    <!-- IMAGE -->
                    <div class="overflow-hidden rounded-top">
                        <img src="{{ asset('images/products/'.$product->image) }}"
                             class="card-img-top product-img"
                             alt="{{ $product->product_name }}">
                    </div>

                    <div class="card-body text-center d-flex flex-column">

                        <h6 class="fw-bold mb-2">
                            {{ $product->product_name }}
                        </h6>

                        <p class="text-primary fw-bold fs-5 mb-3">
                            Ksh {{ number_format($product->price, 2) }}
                        </p>

                        <div class="mt-auto position-relative" style="z-index:2;">

                            <!-- ADD TO CART BUTTON -->
                            <button type="button"
                                class="btn btn-success btn-sm w-100 addBtn"
                                data-id="{{ $product->id }}"
                                data-price="{{ $product->price }}"
                                @if($currentQty > 0) style="display:none;" @endif>
                                Add to Cart
                            </button>

                            <!-- QTY CONTROLS -->
                            <div class="d-flex justify-content-center align-items-center gap-2 mt-2 qtyBox 
                                @if($currentQty == 0) d-none @endif"
                                data-id="{{ $product->id }}">

                                <button class="btn btn-outline-dark btn-sm minusBtn">−</button>

                                <span class="fw-bold qtyDisplay">
                                    {{ $currentQty > 0 ? $currentQty : 1 }}
                                </span>

                                <button class="btn btn-outline-dark btn-sm plusBtn">+</button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links() }}
    </div>

</div>

<style>
.product-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}
.product-img {
    height: 220px;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.product-card:hover .product-img {
    transform: scale(1.05);
}
</style>

<script>
document.querySelectorAll('.product-card').forEach(card => {

    const addBtn = card.querySelector('.addBtn');
    const qtyBox = card.querySelector('.qtyBox');

    if (!addBtn) return;

    const productId = addBtn.dataset.id;
    let quantity = qtyBox && !qtyBox.classList.contains('d-none')
        ? parseInt(qtyBox.querySelector('.qtyDisplay').innerText)
        : 1;

    const plusBtn = card.querySelector('.plusBtn');
    const minusBtn = card.querySelector('.minusBtn');

    const cartCountEl = document.getElementById('cartCount');
    const cartNavTotalEl = document.getElementById('cartTotal');

    function money(v){
        return parseFloat(v).toLocaleString(undefined,{
            minimumFractionDigits:2
        });
    }

    function updateServer() {
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
        });
    }

    addBtn.addEventListener('click', function(e){
        e.stopPropagation();

        quantity = 1;
        updateServer();

        addBtn.style.display = 'none';
        qtyBox.classList.remove('d-none');
        qtyBox.querySelector('.qtyDisplay').innerText = quantity;
    });

    if (plusBtn) {
        plusBtn.addEventListener('click', function(e){
            e.stopPropagation();
            quantity++;
            qtyBox.querySelector('.qtyDisplay').innerText = quantity;
            updateServer();
        });
    }

    if (minusBtn) {
        minusBtn.addEventListener('click', function(e){
            e.stopPropagation();
            if(quantity > 1){
                quantity--;
                qtyBox.querySelector('.qtyDisplay').innerText = quantity;
                updateServer();
            }
        });
    }

});
</script>

@endsection
