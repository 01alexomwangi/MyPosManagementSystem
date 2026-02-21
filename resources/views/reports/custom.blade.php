@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Order Reports</h4>
    </div>

    {{-- ================= SESSION MESSAGES ================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= VALIDATION ERRORS ================= --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Something went wrong:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ================= ORDERS TABLE ================= --}}
    @if(isset($orders) && $orders->count() > 0)

        <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Order Status</th>
                    <th>Source</th>
                    <th>Cashier</th>
                    <th>Location</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th class="text-end">Total</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            @foreach($orders as $order)

            @php
                $payment = $order->payments->first();
            @endphp

            <tr>
                <td>{{ $order->id ?? '-' }}</td>

                <td>
                    {{ optional($order->created_at)->format('Y-m-d H:i') ?? '-' }}
                </td>

                {{-- ORDER STATUS --}}
                <td>
                    @switch($order->status)
                        @case('pending_payment')
                            <span class="badge bg-warning text-dark">Pending Payment</span>
                            @break
                        @case('processing')
                            <span class="badge bg-primary">Processing</span>
                            @break
                        @case('completed')
                            <span class="badge bg-success">Completed</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                            @break
                        @default
                            <span class="badge bg-secondary">
                                {{ ucfirst($order->status ?? 'unknown') }}
                            </span>
                    @endswitch
                </td>

                {{-- SOURCE --}}
                <td>
                    @if($order->source === 'online')
                        <span class="badge bg-primary">Online</span>
                    @elseif($order->source === 'pos')
                        <span class="badge bg-secondary">POS</span>
                    @else
                        <span class="badge bg-dark">
                            {{ ucfirst($order->source ?? 'unknown') }}
                        </span>
                    @endif
                </td>

                {{-- CASHIER --}}
                <td>
                    {{ optional($order->user)->name ?? 'N/A' }}
                </td>

                {{-- LOCATION --}}
                <td>
                    {{ optional($order->location)->name ?? 'N/A' }}
                </td>

                {{-- PAYMENT METHOD --}}
                <td>
                    @if($payment)
                        @switch($payment->method)
                            @case('cash')
                                <span class="badge bg-dark">Cash</span>
                                @break
                            @case('littlepay')
                                <span class="badge bg-info text-dark">LittlePay</span>
                                @break
                            @default
                                <span class="badge bg-secondary">
                                    {{ ucfirst($payment->method) }}
                                </span>
                        @endswitch
                    @else
                        <span class="text-muted">No Payment</span>
                    @endif
                </td>

                {{-- PAYMENT STATUS --}}
                <td>
                    @if($payment)
                        @switch($payment->status)
                            @case('success')
                                <span class="badge bg-success">Success</span>
                                @break
                            @case('pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @break
                            @case('failed')
                                <span class="badge bg-danger">Failed</span>
                                @break
                            @default
                                <span class="badge bg-secondary">
                                    {{ ucfirst($payment->status) }}
                                </span>
                        @endswitch
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>

                <td class="text-end">
                    {{ number_format($order->total ?? 0, 2) }}
                </td>

                {{-- VERIFY BUTTON --}}
                <td>

    {{-- ================= STATUS UPDATE (ONLINE ONLY) ================= --}}
    @if($order->source === 'online')
        <form method="POST"
              action="{{ route('orders.updateStatus', $order->id) }}"
              class="mb-1">
            @csrf

            <select name="status"
                    onchange="this.form.submit()"
                    class="form-select form-select-sm">

                <option disabled selected>Change Status</option>

                <option value="processing"
                    {{ $order->status == 'processing' ? 'selected' : '' }}>
                    Processing
                </option>

                <option value="completed"
                    {{ $order->status == 'completed' ? 'selected' : '' }}>
                    Completed
                </option>

                <option value="cancelled"
                    {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>
            </select>
        </form>
    @endif


    {{-- ================= VERIFY (FOR RECONCILIATION) ================= --}}
    @if(
        auth()->check() &&
        auth()->user()->role === 'admin' &&
        $payment &&
        $payment->method === 'littlepay' &&
        $payment->status === 'pending'
    )
        <form method="POST"
              action="{{ route('payments.verify', $payment->id) }}"
              onsubmit="return confirm('Are you sure you want to verify this payment?')">
            @csrf
            <button type="submit"
                    class="btn btn-sm btn-warning">
                Verify
            </button>
        </form>
    @elseif($order->source !== 'online')
        <span class="text-muted">Handled in Shop</span>
    @endif

</td>
            </tr>

            @endforeach

            </tbody>
        </table>
        </div>

    @else
        <div class="alert alert-warning">
            No orders found.
        </div>
    @endif

</div>
@endsection