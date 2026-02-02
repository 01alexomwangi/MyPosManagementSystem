@extends('layouts.app')

@section('content')
@include('reports.base', [
    'reportTitle' => 'Daily Sales Report',
    'reportPeriod' => \Carbon\Carbon::parse($today)->format('d M Y')
])
@endsection
