@extends('layouts.app')

@section('content')
<div class="container">

    <h4 class="mb-4">Sales Reports</h4>

    {{-- =======================
        DATE FILTER FORM
    ======================== --}}
    <form method="GET" action="{{ route('reports.custom') }}" class="row g-3 mb-4">

        <div class="col-md-4">
            <label class="form-label">From</label>
            <input type="date" name="from" value="{{ $from ?? '' }}" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">To</label>
            <input type="date" name="to" value="{{ $to ?? '' }}" class="form-control">
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">
                <i class="fa fa-sInvalidArgumentException
View [reports.sales] not found.
http://127.0.0.1:8000/reports/daily
Hide solutions
reports.sales was not found.
Are you sure the view exists and is a .blade.php file?

Stack trace
Request
App
User
Context
earch"></i> Generate Report
            </button>
        </div>

    </form>

    {{-- =======================
        QUICK LINKS
    ======================== --}}
    <div class="mb-4">
        <a href="{{ route('reports.daily') }}" class="btn btn-outline-secondary btn-sm">Today</a>
        <a href="{{ route('reports.weekly') }}" class="btn btn-outline-secondary btn-sm">This Week</a>
        <a href="{{ route('reports.monthly') }}" class="btn btn-outline-secondary btn-sm">This Month</a>
    </div>

    {{-- =======================
        REPORT RESULTS
    ======================== --}}
    @if(isset($sales))

        <h5 class="mb-3">
            Report
            @if($from && $to)
                from {{ $from }} to {{ $to }}
            @elseif($from)
                for {{ $from }}
            @endif
        </h5>

        @if($sales->count() > 0)

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Cashier</th>
                        <th>Location</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td>{{ $sale->location->name }}</td>
                            <td class="text-end">
                                {{ number_format($sale->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- TOTAL --}}
            <div class="text-end fw-bold mt-3">
                Grand Total:
                {{ number_format($sales->sum('total'), 2) }}
            </div>

        @else
            <div class="alert alert-warning">
                No sales found for the selected period.
            </div>
        @endif

    @endif

</div>
@endsection
>
