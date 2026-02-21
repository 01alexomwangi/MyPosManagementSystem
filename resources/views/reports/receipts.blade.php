@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Receipts</h4>

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="from" class="form-control" value="{{ $from ?? '' }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" class="form-control" value="{{ $to ?? '' }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Status</th>
                <th>Location</th>
                <th>Source / Staff</th>
                <th class="text-end">Total</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>

                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>

                    <td>
                        <span class="badge bg-secondary">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>

                    <td>{{ optional($order->location)->name ?? '-' }}</td>

                    <td>
                        @if($order->source === 'online')
                            <span class="badge bg-primary">Online</span>
                        @elseif($order->source === 'pos')
                            {{ optional($order->user)->name ?? 'POS' }}
                        @endif
                    </td>

                    <td class="text-end">
                        {{ number_format($order->total, 2) }}
                    </td>

                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="btn btn-sm btn-success">
                           Print
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        No receipts found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection