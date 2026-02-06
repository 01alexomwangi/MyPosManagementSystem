{{-- @extends('layouts.store')

@section('title', 'Your Cart')

@section('content')
<div class="container py-5">

    <h2 class="mb-4">Your Shopping Cart</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(count($cart) > 0)
    <form method="POST" action="{{ route('customer.cart.checkout') }}">
        @csrf
        <input type="hidden" name="location_id" value="1"> <!-- adjust if needed -->

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>
                        <input type="number" name="items[{{ $loop->index }}][quantity]" value="{{ $item['quantity'] }}" class="form-control" min="1">
                        <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item['product_id'] }}">
                        <input type="hidden" name="items[{{ $loop->index }}][price]" value="{{ $item['price'] }}">
                    </td>
                    <td>{{ number_format($item['price'], 2) }}</td>
                    <td>
                        <input type="hidden" name="items[{{ $loop->index }}][total_amount]" value="{{ $item['total_amount'] }}">
                        {{ number_format($item['total_amount'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Total: Ksh {{ number_format(array_sum(array_column($cart, 'total_amount')),2) }}</h4>

        <button type="submit" class="btn btn-primary mt-3">Checkout</button>
    </form>
    @else
        <p>Your cart is empty. <a href="{{ route('store.index') }}">Browse products</a></p>
    @endif

</div>
@endsection --}}
