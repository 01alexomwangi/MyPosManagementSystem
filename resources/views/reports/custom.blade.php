@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Order Reports</h3>
            <small class="text-muted">Manage and monitor all transactions</small>
        </div>
    </div>

    <!-- Session Messages -->
    @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $color)
        @if(session($key))
            <div class="alert alert-{{ $color }} alert-dismissible fade show shadow-sm rounded-3">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3">
            <strong>Something went wrong:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Orders Table Card -->
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body p-0">

            @if(isset($orders) && $orders->count() > 0)

                <div class="table-responsive">

                    <table class="table align-middle mb-0 table-hover">

                        <thead style="background: #f8f9fa;">
                            <tr class="text-muted text-uppercase small">
                                <th>#</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Cashier</th>
                                <th>Location</th>
                                <th>Payment</th>
                                <th>Pay Status</th>
                                <th class="text-end">Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            
                        @foreach($orders as $order)
                     {{-- @if ($order->id === 124) --}}


                        @php
                            $payment = $order->payments->first();
                        @endphp

                        <tr>

                            <td class="fw-semibold">{{ $order->id ?? '-' }}</td>

                            <td>
                                <small class="text-muted">
                                    {{ optional($order->created_at)->format('Y-m-d H:i') ?? '-' }}
                                </small>
                            </td>

                            <!-- ORDER STATUS -->
                            <td>
                                @switch($order->status)
                                    @case('pending_payment')
                                        <span class="badge bg-warning-subtle text-dark px-3 py-2 rounded-pill">Pending Payment</span>
                                        @break
                                    @case('processing')
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Processing</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Cancelled</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                            {{ ucfirst($order->status ?? 'unknown') }}
                                        </span>
                                @endswitch
                            </td>

                            <!-- SOURCE -->
                            <td>
                                @if($order->source === 'online')
                                    <span class="badge bg-primary rounded-pill px-3">Online</span>
                                @elseif($order->source === 'pos')
                                    <span class="badge bg-dark rounded-pill px-3">POS</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3">
                                        {{ ucfirst($order->source ?? 'unknown') }}
                                    </span>
                                @endif
                            </td>

                            <td>{{ optional($order->user)->name ?? 'N/A' }}</td>
                            <td>{{ optional($order->location)->name ?? 'N/A' }}</td>

                            <!-- PAYMENT METHOD -->
                            <td>
                                @if($payment)
                                    @switch($payment->method)
                                        @case('cash')
                                            <span class="badge bg-dark rounded-pill px-3">Cash</span>
                                            @break
                                        @case('littlepay')
                                            <span class="badge bg-info text-dark rounded-pill px-3">LittlePay</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary rounded-pill px-3">
                                                {{ ucfirst($payment->method) }}
                                            </span>
                                    @endswitch
                                @else
                                    <span class="text-muted small">No Payment</span>
                                @endif
                            </td>

                            <!-- PAYMENT STATUS -->
                            <td>
                                @if($payment)
                                    @switch($payment->status)
                                        @case('success')
                                            <span class="badge bg-success rounded-pill px-3">Success</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-danger rounded-pill px-3">Failed</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary rounded-pill px-3">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                    @endswitch
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>

                            <td class="text-end fw-bold">
                                {{ number_format($order->total ?? 0, 2) }}
                            </td>

                            <!-- ACTION -->
                            <td>

                                @if($order->source === 'online')
                                    <form method="POST"
                                          action="{{ route('orders.updateStatus', $order->id) }}"
                                          class="mb-2">
                                        @csrf
                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="form-select form-select-sm rounded-pill shadow-sm">
                                            <option disabled selected>Change Status</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                                Processing
                                            </option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                                Completed
                                            </option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                                Cancelled
                                            </option>
                                        </select>
                                    </form>
                                @endif

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
                                                class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm">
                                            Verify
                                        </button>
                                    </form>
                                @elseif($order->source !== 'online')
                                    <small class="text-muted">Handled in Shop</small>
                                @endif

                            </td>
                        </tr>
                      {{-- @endif --}}
                        @endforeach

                        </tbody>
                    </table>

                </div>

            @else

                <div class="p-4">
                    <div class="alert alert-warning mb-0 rounded-3">
                        No orders found.
                    </div>
                </div>

            @endif

        </div>
    </div>

</div>
@endsection