@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">

            <div class="text-center mb-3">
                <h3>{{ config('app.name') }}</h3>
                <small>Daily Sales Report</small><br>
                <small>Date: {{ \Carbon\Carbon::parse($today)->format('d M Y') }}</small>
            </div>

            <hr>

            @if($sales->isEmpty())
                <p>No sales found for today.</p>
            @else
                @foreach($sales->groupBy('location_id') as $locationId => $locationSales)
                    <h5>Location: {{ $locationSales->first()->location->name ?? 'N/A' }}</h5>
                    @foreach($locationSales as $sale)
                        <div class="mb-3">
                            <p>
                                <strong>Receipt #:</strong> {{ $sale->id }} <br>
                                <strong>Cashier:</strong> {{ $sale->user->name ?? '' }} <br>
                                <strong>Payment Method:</strong> {{ ucfirst($sale->payment_method ?? 'N/A') }}
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

                            <p>
                                <strong>Total:</strong> {{ number_format($sale->total,2) }} <br>
                                <strong>Paid:</strong> {{ number_format($sale->paid,2) }} <br>
                                <strong>Balance:</strong> {{ number_format($sale->balance,2) }}
                            </p>
                            <hr>
                        </div>
                    @endforeach
                @endforeach
            @endif

            <div class="text-center mt-3">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fa fa-print"></i> Print Report
                </button>

                <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                    Back to POS
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
