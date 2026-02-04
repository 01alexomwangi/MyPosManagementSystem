@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row">

        <!-- LEFT SIDE: PRODUCTS GRID & CART TABLE -->
        <div class="col-md-9">

            <!-- PRODUCTS GRID -->
            <div class="row mb-4">
                @foreach($products as $product)
                <div class="col-md-3 mb-3">
                  <div class="card h-100 product-card">
                    <div class="card-body text-center p-2">

                        {{-- PRODUCT IMAGE --}}
                        <div class="product-image mb-2">
                            @if($product->image)
                                <img src="{{ asset('images/products/'.$product->image) }}"
                                     alt="{{ $product->product_name }}">
                            @else
                                <img src="{{ asset('images/products/default.png') }}"
                                     alt="No image">
                            @endif
                        </div>

                        {{-- PRODUCT NAME --}}
                        <h6 class="mb-1 font-weight-bold">
                            {{ $product->product_name }}
                        </h6>

                        {{-- PRICE --}}
                        <span class="badge badge-primary mb-1">
                            KES {{ number_format($product->price, 2) }}
                        </span>

                        {{-- STOCK --}}
                        <div class="small text-muted mb-2">
                            Stock: {{ $product->quantity }}
                        </div>

                        {{-- ADD TO CART --}}
                        <button type="button"
                                class="btn btn-success btn-sm btn-block add-to-cart"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->product_name }}"
                                data-price="{{ $product->price }}"
                                {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                            <i class="fa fa-cart-plus"></i> Add
                        </button>

                    </div>
                  </div>
                </div>
                @endforeach
            </div>

            <!-- CART TABLE -->
            <div class="card">
                <div class="card-header"><h4>Cart</h4></div>
                <div class="card-body">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="addMoreProduct">
                                <!-- Products added via JS -->
                            </tbody>
                        </table>

                        <!-- TOTAL & PAYMENT -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <h5>Total: <span class="total">0.00</span></h5>
                            </div>
                            <div class="col-md-4">
                                <label>Paid Amount</label>
                                <input type="number" name="paid_amount" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Balance</label>
                                <input type="number" name="balance" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mt-3">
                            <input type="hidden" name="total" id="total_input">
                            <button type="submit" class="btn btn-primary btn-block">Complete Sale</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- RIGHT SIDE: CUSTOMER INFO -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header"><h4>Customer Info (Optional)</h4></div>
                <div class="card-body">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control mb-2">

                    <label>Customer Phone</label>
                    <input type="text" name="customer_phone" class="form-control mb-2">

                    <label>Payment Method</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_method" value="cash" checked>
                        <label class="form-check-label">Cash</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer">
                        <label class="form-check-label">Bank Transfer</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_method" value="credit_card">
                        <label class="form-check-label">Credit Card</label>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function(){

    // ADD PRODUCT TO CART
    $('.add-to-cart').click(function(){
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');

        var rowCount = $('.addMoreProduct tr').length;

        // Check if product already in cart
        var exists = false;
        $('.addMoreProduct tr').each(function(){
            var pid = $(this).find('.product_id_hidden').val();
            if(pid == id){
                // increase quantity
                var qtyInput = $(this).find('.quantity');
                qtyInput.val(parseInt(qtyInput.val()) + 1);
                updateRowTotal($(this));
                exists = true;
            }
        });
        if(exists) return;

        var tr = `<tr>
            <td class="no">${rowCount+1}</td>
            <td>${name}</td>
            <input type="hidden" class="product_id_hidden" name="items[${rowCount}][product_id]" value="${id}">
            <td><input type="number" name="items[${rowCount}][quantity]" class="form-control quantity" value="1" min="1"></td>
            <td><input type="number" name="items[${rowCount}][price]" class="form-control price" value="${price}" readonly></td>
            <td><input type="number" name="items[${rowCount}][total_amount]" class="form-control total_amount" value="${price}" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm delete"><i class="fa fa-times-circle"></i></button></td>
        </tr>`;
        $('.addMoreProduct').append(tr);
        calculateTotal();
    });

    // DELETE ROW
    $('.addMoreProduct').on('click', '.delete', function(){
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // UPDATE TOTAL WHEN QUANTITY CHANGES
    $('.addMoreProduct').on('keyup change', '.quantity', function(){
        var tr = $(this).closest('tr');
        updateRowTotal(tr);
    });

    function updateRowTotal(tr){
        var qty = parseFloat(tr.find('.quantity').val()) || 0;
        var price = parseFloat(tr.find('.price').val()) || 0;
        tr.find('.total_amount').val((qty * price).toFixed(2));
        calculateTotal();
    }

    function calculateTotal(){
        var total = 0;
        $('.total_amount').each(function(){
            total += parseFloat($(this).val()) || 0;
        });
        $('.total').text(total.toFixed(2));
        $('#total_input').val(total.toFixed(2));

        var paid = parseFloat($('input[name="paid_amount"]').val()) || 0;
        $('input[name="balance"]').val((paid - total).toFixed(2));
    }

    // UPDATE BALANCE WHEN PAID AMOUNT CHANGES
    $('input[name="paid_amount"]').on('keyup change', function(){
        calculateTotal();
    });

});
</script>

<style>
.product-image {
    height: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

.product-card {
    transition: transform 0.15s ease-in-out;
}

.product-card:hover {
    transform: scale(1.03);
}
</style>

@endsection
