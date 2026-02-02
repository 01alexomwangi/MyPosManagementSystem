<div class="mb-3">
    <p>
        <strong>Receipt #:</strong> {{ $sale->id }} <br>
        <strong>Cashier:</strong> {{ $sale->user->name ?? 'N/A' }} <br>
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
                    <td>{{ $item->product->product_name ?? '' }}</td>
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
