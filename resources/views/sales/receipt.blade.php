@extends('layouts.app')

@section('content')
<div class="container mt-8">
    <div class="card">
        <div class="card-body">

            <div class="text-center mb-3">
                <h3>{{ config('app.name') }}</h3>
                <small>{{ $sale->location->name ?? '' }}</small><br>
                <small>Date: {{ $sale->created_at->format('d M Y H:i') }}</small>
            </div>

            <hr>

            <p>
                <strong>Cashier:</strong> {{ $sale->user->name }} <br>
                <strong>Receipt #:</strong> {{ $sale->id }}
            </p>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>{{ $item->product->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price,2) }}</td>
                        <td>{{ number_format($item->subtotal,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <p>
                <strong>Total:</strong> {{ number_format($sale->total,2) }} <br>
                <strong>Paid:</strong> {{ number_format($sale->paid,2) }} <br>
                <strong>Balance:</strong> {{ number_format($sale->balance,2) }}
            </p>

            <div class="text-center mt-3">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fa fa-print"></i> Print Receipt
                </button>

                <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                    New Sale
                </a>
            </div>

        </div>
    </div>
</div>
@endsection


<style>
@media print {
    button, a { display: none; }
    body { font-size: 12px; }
}
</style>