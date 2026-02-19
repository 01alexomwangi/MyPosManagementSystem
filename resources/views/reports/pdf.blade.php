<!DOCTYPE html>
<html>
<head>
    <title>Orders Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 5px; }
        th { background-color: #f2f2f2; }
        h3 { text-align: center; }
    </style>
</head>
<body>
    <h3>Orders Report</h3>

    @if($from && $to)
        <p>From {{ $from }} to {{ $to }}</p>
    @elseif($from)
        <p>For {{ $from }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Cashier</th>
                <th>Location</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>
               @if($order->source === 'online')
                  Online
               @else
            {{ optional($order->user)->name ?? '-' }}
              @endif
            </td>

                <td>{{ optional($order->location)->name ?? '-' }}</td>

                <td style="text-align: right;">{{ number_format($order->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: right; font-weight: bold;">
        Grand Total: {{ number_format($orders->sum('total'), 2) }}
    </p>
</body>
</html>
