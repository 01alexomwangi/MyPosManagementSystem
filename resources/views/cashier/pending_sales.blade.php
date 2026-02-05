@extends('layouts.app')

@section('title', 'Pending Sales')

@section('content')
<div class="container py-5">

    <h2 class="mb-4">Pending Sales</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($pendingSales as $sale)
    <div class="card mb-3">
        <div class="card-header">
            Customer: {{ $sale->customer->name ?? 'Guest' }} | Total: Ksh {{ number_format($sale->total,2) }}
            <form method="POST" action="{{ route('cashier.complete', $sale->id) }}" class="d-inline float-right">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Complete Payment</button>
            </form>
        </div>
        <div class="card-body">
            <ul>
                @foreach($sale->items as $item)
                    <li>{{ $item->product->product_name }} x {{ $item->quantity }} = Ksh {{ number_format($item->total_amount,2) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endforeach

    @if(count($pendingSales) == 0)
        <p>No pending sales at the moment.</p>
    @endif

</div>
@endsection
