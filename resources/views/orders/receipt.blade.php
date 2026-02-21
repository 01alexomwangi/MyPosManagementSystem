@extends('layouts.app')

@section('content')
<div class="container mt-4 d-flex justify-content-center">
    <div class="card shadow-sm" style="width: 420px;">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="text-center">
                <h5 class="mb-0">{{ config('app.name') }}</h5>
                <small>{{ optional($order->location)->name }}</small><br>
                <small>{{ optional($order->location)->address }}</small><br>
                <small>{{ $order->created_at->format('d M Y H:i') }}</small>
            </div>

            <hr>

            {{-- ORDER INFO --}}
            <p class="mb-1">
                <strong>Receipt #:</strong> {{ $order->order_number }} <br>
                <strong>Type:</strong> {{ strtoupper($order->source) }} <br>
                <strong>Status:</strong>
                @if($order->status === 'pending_payment')
                    <span class="badge bg-warning">Pending Payment</span>
                @elseif($order->status === 'processing')
                    <span class="badge bg-primary">Processing</span>
                @elseif($order->status === 'completed')
                    <span class="badge bg-success">Completed</span>
                @elseif($order->status === 'cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                @endif
            </p>

            @if($order->source === 'online')
                <p>
                    <strong>Delivery:</strong>
                    @if($order->delivery_status === 'pending')
                        <span class="badge bg-secondary">Pending</span>
                    @elseif($order->delivery_status === 'out_for_delivery')
                        <span class="badge bg-info">Out For Delivery</span>
                    @elseif($order->delivery_status === 'delivered')
                        <span class="badge bg-success">Delivered</span>
                    @endif
                </p>
            @endif

            <hr>

            {{-- ITEMS --}}
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">
                                {{ number_format($item->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            @php
                $paid = $order->payments
                    ->where('status','success')
                    ->sum('amount');
            @endphp

            <div class="text-end">
                <p><strong>Total:</strong> {{ number_format($order->total, 2) }}</p>
                <p><strong>Paid:</strong> {{ number_format($paid, 2) }}</p>
                <p><strong>Balance:</strong> {{ number_format($order->total - $paid, 2) }}</p>
            </div>

            <hr>


            <div class="text-center mt-3">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    Print
                </button>
            </div>

        </div>
    </div>
</div>
@endsection