<div class="container mt-4">
    <div class="card">
        <div class="card-body">

            <div class="text-center mb-3">
                <h3>{{ config('app.name') }}</h3>
                <small>{{ $reportTitle }}</small><br>
                <small>{{ $reportPeriod }}</small>
            </div>

            <hr>

            @if($sales->isEmpty())
                <p>No sales found.</p>
            @else
                @if(auth()->user()->hasAnyRole(['admin','manager']))
                    @foreach($sales->groupBy('location_id') as $locationSales)
                        <h5>Location: {{ $locationSales->first()->location->name ?? 'N/A' }}</h5>

                        @foreach($locationSales as $sale)
                            @include('reports.partials.sale')
                        @endforeach
                    @endforeach
                @else
                    {{-- Cashier --}}
                    @foreach($sales as $sale)
                        @include('reports.partials.sale')
                    @endforeach
                @endif
            @endif

            <div class="text-center mt-3">
                <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">Back to POS</a>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    button, a { display: none; }
    body { font-size: 12px; }
}
</style>
