@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row">

        <!-- LEFT SIDE: POS TABLE -->
        <div class="col-md-9">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center"> 
                    <h4>POS - Create Sale</h4>
                    <a href="#" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#addproduct">
                        <i class="fa fa-plus"></i> Add New Product
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf

                         <input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">     {{-- attach sale to location it happened  --}}

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th><button type="button" class="btn btn-sm btn-success add_more"><i class="fa fa-plus-circle"></i></button></th>
                                </tr>
                            </thead>
                            <tbody class="addMoreProduct">
                                <tr>
                                    <td class="no">1</td>
                                    <td>
                                        <select name="items[0][product_id]" class="form-control product_id">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1"></td>
                                    <td><input type="number" name="items[0][price]" class="form-control price" readonly></td>
                                    <td><input type="number" name="items[0][total_amount]" class="form-control total_amount" readonly></td>
                                    <td><button type="button" class="btn btn-danger btn-sm delete"><i class="fa fa-times-circle"></i></button></td>
                                </tr>
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

{{-- Add Product Modal (same as your current products blade) --}}
<div class="modal right fade" id="addproduct" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Product</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <form action="{{ route('products.store') }}" method="POST">
              @csrf
              <div class="form-group">
                  <label>Product Name</label>
                  <input type="text" name="product_name" class="form-control" required>
              </div>
              <div class="form-group">
                  <label>Price</label>
                  <input type="number" name="price" class="form-control" required>
              </div>
              <div class="form-group">
                  <label>Quantity</label>
                  <input type="number" name="quantity" class="form-control" required>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-primary btn-block">Save Product</button>
              </div>
          </form>
      </div>
    </div>
  </div>
  </div>

  <style>
  .modal.right .modal-dialog{
    top: 0;
    right: 0;
    margin-right: 19vh;
  }
  .modal.fade:not(.in).right .modal-dialog{
    transform: translate3d(25%,0,0);
  }
  </style>
  @endsection

  @section('script')
   <script>
  $(document).ready(function(){

    // ADD ROW
    $('.add_more').click(function(){
        var productHtml = $('.product_id').html();
        var rowCount = $('.addMoreProduct tr').length;
        var tr = `<tr>
            <td class="no">${rowCount+1}</td>
            <td><select name="items[${rowCount}][product_id]" class="form-control product_id">${productHtml}</select></td>
            <td><input type="number" name="items[${rowCount}][quantity]" class="form-control quantity" value="1" min="1"></td>
            <td><input type="number" name="items[${rowCount}][price]" class="form-control price" readonly></td>
            <td><input type="number" name="items[${rowCount}][total_amount]" class="form-control total_amount" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm delete"><i class="fa fa-times-circle"></i></button></td>
        </tr>`;
        $('.addMoreProduct').append(tr);
    });

    // DELETE ROW
    $('.addMoreProduct').on('click', '.delete', function(){
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // UPDATE PRICE & TOTAL WHEN PRODUCT CHANGES
    $('.addMoreProduct').on('change', '.product_id', function(){
        var tr = $(this).closest('tr');
        var price = $('option:selected', this).data('price') || 0;
        tr.find('.price').val(price);
        updateRowTotal(tr);
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

        $('#total_input').val(total.toFixed(2)); //added  shown to user,submitted to controller,storednin db

        var paid = parseFloat($('input[name="paid_amount"]').val()) || 0;
        $('input[name="balance"]').val((paid - total).toFixed(2));
    }

    // UPDATE BALANCE WHEN PAID AMOUNT CHANGES
    $('input[name="paid_amount"]').on('keyup change', function(){
        calculateTotal();
    });

    });
    </script>
    @endsection
