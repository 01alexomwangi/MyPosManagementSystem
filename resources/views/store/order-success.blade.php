@extends('layouts.store')

@section('content')
<div class="container py-5">

<h2>Order Details</h2>

<p><strong>Order Number:</strong> {{ $order->order_number }}</p>

<p>
<strong>Status:</strong>
@if($order->status === 'pending_payment')
    <span class="text-warning">Awaiting Payment</span>
@elseif($order->status === 'processing')
    <span class="text-primary">Processing</span>
@elseif($order->status === 'completed')
    <span class="text-success">Completed</span>
@elseif($order->status === 'cancelled')
    <span class="text-danger">Cancelled</span>
@endif
</p>

@if($order->source === 'online')
<p>
<strong>Delivery Status:</strong>
@if($order->delivery_status === 'pending')
    Pending
@elseif($order->delivery_status === 'out_for_delivery')
    Out For Delivery
@elseif($order->delivery_status === 'delivered')
    Delivered
@endif
</p>
@endif

<hr>

<h4>Items</h4>

@foreach($order->items as $item)
<div>
    {{ $item->product->product_name }}
    x {{ $item->quantity }}
    - KES {{ number_format($item->amount, 2) }}
</div>
@endforeach

<hr>

<p><strong>Total:</strong> KES {{ number_format($order->total, 2) }}</p>

</div>
@endsection