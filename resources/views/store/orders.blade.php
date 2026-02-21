@extends('layouts.store')

@section('content')
<div class="container py-5">

<h3>My Orders</h3>
<hr>

@forelse($orders as $order)
<div class="card mb-3 p-3">

    <strong>Order #{{ $order->order_number }}</strong><br>

    Status:
    @if($order->status === 'pending_payment')
        <span class="badge bg-warning">Awaiting Payment</span>
    @elseif($order->status === 'processing')
        <span class="badge bg-primary">Processing</span>
    @elseif($order->status === 'completed')
        <span class="badge bg-success">Completed</span>
    @elseif($order->status === 'cancelled')
        <span class="badge bg-danger">Cancelled</span>
    @endif
    <br>

    @if($order->source === 'online')
        <strong>Delivery:</strong>
        @if($order->delivery_status === 'pending')
            Pending
        @elseif($order->delivery_status === 'out_for_delivery')
            Out For Delivery
        @elseif($order->delivery_status === 'delivered')
            Delivered
        @endif
        <br>
    @endif

    Total: KES {{ number_format($order->total, 2) }}

    <div class="mt-2">
        <a href="{{ route('store.order.success', $order->id) }}"
           class="btn btn-sm btn-outline-dark">
           View Details
        </a>
    </div>

</div>
@empty
<p>You have no orders yet.</p>
@endforelse

</div>
@endsection