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

                          <span class="badge bg-secondary">
                                {{ $product->location->name ?? 'No Branch' }}
                            </span>

                        <p class="text-primary fw-bold fs-5 mb-3">
                            Ksh {{ number_format($product->price, 2) }}
                        </p>


                        <div class="mt-auto position-relative" style="z-index:2;">

                                                    <!-- ADD TO CART BUTTON -->
                                                @php
                            $selectedLocation = session('selected_location');
                        @endphp

                        @if(!$selectedLocation)

                            <button type="button"
                                    class="btn btn-warning btn-sm w-100"
                                    disabled>
                                Select location First
                            </button>

                        @elseif($product->location_id == $selectedLocation)

                            <button type="button"
                                    class="btn btn-success btn-sm w-100 addBtn"
                                    data-id="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    @if($currentQty > 0) style="display:none;" @endif>
                                Add to Cart
                            </button>

                        @else

                            <button type="button"
                                    class="btn btn-secondary btn-sm w-100"
                                    disabled>
                                Not Available In Selected location
                            </button>

                        @endif

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
    //Looping Through Product Cards
document.querySelectorAll('.product-card').forEach(card => {


    //Grab Elements Inside the Card
    const addBtn = card.querySelector('.addBtn');//he Add to Cart button.
    const qtyBox = card.querySelector('.qtyBox');//the quantity display box with plus/minus buttons.

    if (!addBtn) return;

    //Reads data-id from Blade button , This tells JS which product is being added.
    const productId = addBtn.dataset.id;//Get Product ID and Initial Quantity


    let quantity = qtyBox && !qtyBox.classList.contains('d-none')//Checks if quantity box is visible
        ? parseInt(qtyBox.querySelector('.qtyDisplay').innerText)//If yes, reads current quantity; if no, sets it to 1.
        : 1;//Ensures the initial quantity is correct.

    //Grab Plus and Minus Buttons
    //These buttons will increase or decrease quantity , If they don’t exist, JS will simply skip them.
    const plusBtn = card.querySelector('.plusBtn');
    const minusBtn = card.querySelector('.minusBtn');

    //Grab Cart Display Elements
    //Cart count in the navbar  ,Total cart value in the navbar ,This allows JS to update the cart display dynamically.
    const cartCountEl = document.getElementById('cartCount');
    const cartNavTotalEl = document.getElementById('cartTotal');

      
    function money(v){
        return parseFloat(v).toLocaleString(undefined,{
            minimumFractionDigits:2   //Makes totals of money look professional.
        });
    }

    function updateServer() {//Update Server Function
        fetch("{{ url('/cart/add') }}/" + productId, {//Passes product ID (from URL) and quantity.
            method: 'POST',//Sends POST request to your CartController.
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',//Uses CSRF token for security.
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'quantity=' + quantity
        })
        .then(res => res.json())      //NB this function Shows AJAX + JSON + dynamic UI update.
        .then(data => {
            if(data.success){
                if(cartCountEl) cartCountEl.innerText = data.cartCount;//Updates cart count and total in navbar when response is returned.
                if(cartNavTotalEl) cartNavTotalEl.innerText = money(data.cartTotal);
            }
        });
    }

    addBtn.addEventListener('click', function(e){
        e.stopPropagation();//Prevents event bubbling

        quantity = 1;//Sets quantity to 1 (first addition).
        updateServer();//Calls updateServer() → adds to session cart.

        addBtn.style.display = 'none';//Hides Add button (display:none).
        qtyBox.classList.remove('d-none');//Shows quantity box (d-none removed).
        qtyBox.querySelector('.qtyDisplay').innerText = quantity;//Displays current quantity.
    });

    if (plusBtn) {
        plusBtn.addEventListener('click', function(e){
            e.stopPropagation();
            quantity++;//Increments quantity.
            qtyBox.querySelector('.qtyDisplay').innerText = quantity;//Updates quantity display.
            updateServer();//Sends updated quantity to the server.
        });
    }

    if (minusBtn) {
        minusBtn.addEventListener('click', function(e){
            e.stopPropagation();
            if(quantity > 1){ //Decrements quantity but ensures it doesn’t go below 1.
                quantity--;
                qtyBox.querySelector('.qtyDisplay').innerText = quantity;
                updateServer();
            }
        });
    }

});
</script>

@endsection
