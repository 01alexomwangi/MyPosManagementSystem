@extends('layouts.app')

@section('content')
<div class="container">
    <h4>All Receipts</h4>

    @foreach($sales as $sale)
        <div class="card mb-3 p-2">
            <div class="d-flex justify-content-between">
                <strong>Receipt #{{ $sale->id }}</strong>
                <small>{{ $sale->created_at->format('Y-m-d H:i') }}</small>
            </div>
            <div>
                Cashier: {{ $sale->user->name }} <br>
                Location: {{ $sale->location->name }}
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
                    @foreach($sale->items as $item)
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

    @if($sales->isEmpty())
        <div class="alert alert-warning">No receipts found for the selected period.</div>
    @endif
</div>
@endsection
