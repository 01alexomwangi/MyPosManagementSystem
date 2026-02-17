@extends('layouts.app')

@section('content')
<div class="container">
    <h2>System Logs</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Payload</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->type }}</td>
                <td>
                    <pre>{{ json_encode(json_decode($log->payload), JSON_PRETTY_PRINT) }}</pre>
                </td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
