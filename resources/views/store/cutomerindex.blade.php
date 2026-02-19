@extends('layouts.store')

@section('content')

<div class="container py-5">

    <h3>My Orders</h3>
    <hr>

    @forelse($orders as $order)
        <div class="card mb-3 p-3">
            <strong>Order #{{ $order->order_number }}</strong><br>

            Status:
            @if($order->status === 'paid')
                <span class="text-success">Paid</span>
            @else
                <span class="text-warning">Pending</span>
            @endif
            <br>

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
