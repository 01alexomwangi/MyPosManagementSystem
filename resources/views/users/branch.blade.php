@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Cashiers in Your Branch ({{ auth()->user()->location->name ?? '' }})</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $key => $user)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No cashiers found in your branch.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
