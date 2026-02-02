@extends('layouts.app')

@section('content')
@include('reports.base', [
    'reportTitle' => 'Monthly Sales Report',
    'reportPeriod' => \Carbon\Carbon::create($year, $month)->format('F Y')
])
@endsection
