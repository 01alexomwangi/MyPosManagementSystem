@extends('layouts.store')

@section('content')

<div class="container py-5">

    <h2>Order Successful 🎉</h2>

    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>

    <p>
        <strong>Status:</strong>
        @if($order->status === 'paid')
            <span class="text-success">Paid</span>
        @else
            <span class="text-warning">Pending</span>
        @endif
    </p>

    <p><strong>Delivery Method:</strong> {{ ucfirst($order->delivery_method) }}</p>

    <p><strong>Delivery Fee:</strong> KES {{ number_format($order->delivery_fee, 2) }}</p>

    <p><strong>Total Paid:</strong> KES {{ number_format($order->total, 2) }}</p>

    <hr>

    <h4>Items:</h4>

    @foreach($order->items as $item)
        <div>
            {{ $item->product->product_name }}
            x {{ $item->quantity }}
            - KES {{ number_format($item->amount, 2) }}
        </div>
    @endforeach

</div>

@endsection
