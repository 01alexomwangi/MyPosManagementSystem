@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

    {{-- ALERTS --}}
    @if(session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf

        <div class="row g-4">

            {{-- LEFT SIDE --}}
            <div class="col-lg-9">

                {{-- PRODUCTS --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fa fa-box me-2"></i> Products
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            @foreach($products as $product)
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="card product-card h-100 border-0 shadow-sm rounded-4">

                                        <div class="card-body text-center p-3">

                                            <div class="product-image mb-3">
                                                @if($product->image)
                                                    <img src="{{ asset('images/products/'.$product->image) }}">
                                                @else
                                                    <img src="{{ asset('images/products/default.png') }}">
                                                @endif
                                            </div>

                                            <h6 class="fw-bold mb-1">
                                                {{ $product->product_name }}
                                            </h6>

                                            <div class="badge bg-primary mb-2 px-3 py-2 rounded-pill">
                                                KES {{ number_format($product->price,2) }}
                                            </div>

                                            <div class="small text-muted mb-3">
                                                Stock: {{ $product->quantity }}
                                            </div>

                                            <button type="button"
                                                    class="btn btn-success btn-sm w-100 addProduct rounded-pill"
                                                    data-id="{{ $product->id }}"
                                                    data-name="{{ $product->product_name }}"
                                                    data-price="{{ $product->price }}"
                                                    data-stock="{{ $product->quantity }}"
                                                    {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                                <i class="fa fa-plus me-1"></i> Add
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- CART --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold text-success">
                            <i class="fa fa-shopping-cart me-2"></i> Cart
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th width="120">Qty</th>
                                        <th width="120">Price</th>
                                        <th width="120">Total</th>
                                        <th width="60">Remove</th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody"></tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="row align-items-end mt-3">
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded-4 shadow-sm">
                                    <h4 class="mb-0 fw-bold">
                                        Total:
                                        <span class="text-success">
                                            KES <span id="grandTotal">0.00</span>
                                        </span>
                                    </h4>
                                    <input type="hidden" name="total" id="totalInput">
                                </div>
                            </div>

                            <div class="col-md-4 cashSection">
                                <label class="fw-semibold">Paid Amount</label>
                                <input type="number"
                                       step="0.01"
                                       name="paid_amount"
                                       class="form-control rounded-3 shadow-sm">
                            </div>

                            <div class="col-md-4 cashSection">
                                <label class="fw-semibold">Balance</label>
                                <input type="number"
                                       class="form-control rounded-3 shadow-sm"
                                       id="balance"
                                       readonly>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT SIDE --}}
            <div class="col-lg-3">

                <div class="card border-0 shadow-sm rounded-4 mb-3 sticky-top" style="top:20px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fa fa-credit-card me-2"></i> Payment
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="form-check mb-3 p-2 rounded-3 bg-light">
                            <input type="radio"
                                   class="form-check-input paymentMethod"
                                   name="payment_method"
                                   value="cash"
                                   checked>
                            <label class="form-check-label fw-semibold">
                                Cash
                            </label>
                        </div>

                        <div class="form-check p-2 rounded-3 bg-light">
                            <input type="radio"
                                   class="form-check-input paymentMethod"
                                   name="payment_method"
                                   value="littlepay">
                            <label class="form-check-label fw-semibold">
                                Little Pay (Online)
                            </label>
                        </div>

                    </div>
                </div>

                <button type="submit"
                        class="btn btn-primary w-100 py-3 rounded-4 shadow-sm fw-bold"
                        id="submitBtn">
                    Complete Order
                </button>

            </div>

        </div>
    </form>
</div>
@endsection


@section('script')
<script>
$(document).ready(function(){

    let cartIndex = 0;

    $('.addProduct').click(function(){

        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = parseFloat($(this).data('price'));
        let stock = parseInt($(this).data('stock'));

        let existingRow = $('#cartBody').find('tr[data-id="'+id+'"]');

        if(existingRow.length){
            let qtyInput = existingRow.find('.qty');
            let currentQty = parseInt(qtyInput.val());

            if(currentQty < stock){
                qtyInput.val(currentQty + 1);
                updateRow(existingRow);
            }
            return;
        }

        let row = `
        <tr data-id="${id}">
            <td>${cartIndex + 1}</td>
            <td>${name}</td>

            <input type="hidden"
                   name="items[${cartIndex}][product_id]"
                   value="${id}">

            <td>
                <input type="number"
                       name="items[${cartIndex}][quantity]"
                       class="form-control qty"
                       value="1"
                       min="1"
                       max="${stock}">
            </td>

            <td>
                <input type="text"
                       class="form-control price"
                       value="${price}"
                       readonly>
            </td>

            <td>
                <input type="text"
                       class="form-control rowTotal"
                       value="${price.toFixed(2)}"
                       readonly>
            </td>

            <td>
                <button type="button"
                        class="btn btn-danger btn-sm removeRow">
                    ✕
                </button>
            </td>
        </tr>
        `;

        $('#cartBody').append(row);
        cartIndex++;
        calculateTotal();
    });

    $('#cartBody').on('click','.removeRow',function(){
        $(this).closest('tr').remove();
        calculateTotal();
    });

    $('#cartBody').on('keyup change','.qty',function(){
        updateRow($(this).closest('tr'));
    });

    function updateRow(row){
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let price = parseFloat(row.find('.price').val()) || 0;
        row.find('.rowTotal').val((qty * price).toFixed(2));
        calculateTotal();
    }

    function calculateTotal(){
        let total = 0;

        $('.rowTotal').each(function(){
            total += parseFloat($(this).val()) || 0;
        });

        $('#grandTotal').text(total.toFixed(2));
        $('#totalInput').val(total.toFixed(2));

        let paid = parseFloat($('input[name="paid_amount"]').val()) || 0;
        let balance = paid - total;
        $('#balance').val(balance.toFixed(2));
    }

    $('input[name="paid_amount"]').on('keyup change',function(){
        calculateTotal();
    });

    $('.paymentMethod').change(function(){

        let method = $('input[name="payment_method"]:checked').val();

        if(method === 'littlepay'){
            $('.cashSection').hide();
            $('input[name="paid_amount"]').val('');
            $('#balance').val('');
            $('#submitBtn').text('Proceed to Online Payment');
        } else {
            $('.cashSection').show();
            $('#submitBtn').text('Complete Order');
        }
    });

    $('#orderForm').submit(function(e){

        if($('#cartBody tr').length === 0){
            e.preventDefault();
            alert('Please add at least one product.');
            return false;
        }

        let method = $('input[name="payment_method"]:checked').val();
        let paid = parseFloat($('input[name="paid_amount"]').val()) || 0;
        let total = parseFloat($('#totalInput').val()) || 0;

        if(method === 'cash' && paid < total){
            e.preventDefault();
            alert('Paid amount cannot be less than total.');
            return false;
        }

        $('#submitBtn').prop('disabled', true);
    });

});
</script>

<style>
.product-card:hover{
    transform:scale(1.03);
    transition:0.2s ease-in-out;
}

.product-image{
    height:100px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.product-image img{
    max-height:100%;
    max-width:100%;
    object-fit:contain;
}
</style>
@endsection
