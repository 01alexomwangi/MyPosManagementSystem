@extends('layouts.app')

@section('content')
<div class="container mt-4 d-flex justify-content-center">
    <div class="card shadow-sm" style="width: 380px;">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="text-center">
                <h5 class="mb-0">{{ config('app.name') }}</h5>
                <small>{{ optional($order->location)->name }}</small><br>
                <small>
                    {{ optional($order->location)->address ?? '' }}
                </small><br>
                <small>
                    Date: {{ $order->created_at->format('d M Y H:i') }}
                </small>
            </div>

            <hr class="my-2">

           {{-- ORDER INFO --}}
<p class="mb-1">
    <strong>Receipt #:</strong> {{ $order->order_number ?? $order->id }} <br>

    <strong>Order Type:</strong>
    {{ strtoupper($order->source) }} <br>

    @if($order->source === 'pos')
        <strong>Served By:</strong>
        {{ optional($order->user)->name ?? 'POS User' }} <br>

        @if($order->customer)
            <strong>Customer:</strong>
            {{ $order->customer->name }} <br>
        @else
            <strong>Customer:</strong> Walk-in Customer <br>
        @endif

    @else
        <strong>Customer:</strong>
        {{ optional($order->customer)->name ?? 'Online Customer' }} <br>
    @endif
</p>


            <hr class="my-2">

            {{-- ITEMS --}}
            <table class="table table-sm mb-2">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ optional($item->product)->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($item->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr class="my-2">

            {{-- PAYMENT SUMMARY --}}
            @php
                $paid = $order->payments
                              ->where('status', 'success')
                              ->sum('amount');

                $balance = $order->total - $paid;
            @endphp

            <div class="text-end">
                <p class="mb-1">
                    <strong>Total:</strong>
                    KES {{ number_format($order->total, 2) }}
                </p>

                <p class="mb-1">
                    <strong>Paid:</strong>
                    KES {{ number_format($paid, 2) }}
                </p>

                <p class="mb-0">
                    <strong>Balance:</strong>
                    KES {{ number_format($balance, 2) }}
                </p>
            </div>

            <hr class="my-2">

            {{-- PAYMENT METHOD --}}
            <div class="text-center">
                @if($order->payments->count())
                    <small>
                        Payment Method:
                        {{ strtoupper($order->payments->first()->method) }}
                    </small>
                @endif
            </div>

            <hr class="my-2">

            {{-- FOOTER --}}
            <div class="text-center">
                <small>Thank you for shopping with us!</small><br>
                <small>Powered by {{ config('app.name') }}</small>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="text-center mt-3 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    Print Receipt
                </button>

                <a href="{{ route('orders.index') }}" 
                   class="btn btn-secondary btn-sm">
                    New Sale
                </a>
            </div>

        </div>
    </div>
</div>
@endsection


<style>
/* Thermal receipt styling */
@media print {
    body {
        font-size: 12px;
    }

    .no-print {
        display: none !important;
    }

    .card {
        border: none;
        box-shadow: none;
    }
}

.table td, .table th {
    padding: 4px !important;
}
</style>
