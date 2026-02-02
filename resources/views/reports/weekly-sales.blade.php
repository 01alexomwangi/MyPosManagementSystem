@extends('layouts.app')

@section('content')
@include('reports.base', [
    'reportTitle' => 'Weekly Sales Report',
    'reportPeriod' => $startOfWeek->format('d M Y').' - '.$endOfWeek->format('d M Y')
])
@endsection
