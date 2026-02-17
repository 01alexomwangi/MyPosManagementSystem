@extends('layouts.app')

@section('content')
<div class="container">
    <h4>All Receipts</h4>

    @foreach($orders as $order)
        <div class="card mb-3 p-2">
            <div class="d-flex justify-content-between">
                <strong>Receipt #{{ $order->id }}</strong>
                <small>{{ $order->created_at->format('Y-m-d H:i') }}</small>
            </div>
            <div>
                Cashier: {{ $order->user->name }} <br>
                Location: {{ $order->location->name }}
            </div>
            <table class="table table-sm mt-2">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                            <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end fw-bold">
                Total: {{ number_format($sale->total, 2) }}
            </div>
        </div>
    @endforeach

    @if($orders->isEmpty())
        <div class="alert alert-warning">No receipts found for the selected period.</div>
    @endif
</div>
@endsection
