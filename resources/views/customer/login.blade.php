@extends('layouts.store')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-4 text-center">Customer Login</h3>

                    {{-- Display login error --}}
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ url('/customer/login') }}">
                        @csrf

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>

                    <p class="mt-3 text-center">
                        Don't have an account? <a href="{{ url('/customer/register') }}">Register here</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
