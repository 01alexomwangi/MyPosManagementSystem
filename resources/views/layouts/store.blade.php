<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Little POS Store')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">Little POS Store</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        @php
            $cart = Session::get('cart', []);
            $cartQty = array_sum(array_column($cart, 'quantity'));
            $cartTotal = array_sum(array_column($cart, 'total_amount'));
        @endphp



        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto align-items-center">

                {{-- ACCOUNT --}}
                @if(Session::has('customer_id'))
                    @php $customer = \App\Customer::find(Session::get('customer_id')); @endphp
                    <li class="nav-item">
                        <span class="nav-link">Welcome, {{ $customer->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ url('/customer/logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ url('/customer/login') }}">Login</a></li>
                            <li><a class="dropdown-item" href="{{ url('/customer/register') }}">Register</a></li>
                        </ul>
                    </li>
                @endif

                {{-- CART (JS-CONTROLLED) --}}
                <li class="nav-item ms-3">
                    <a href="{{ route('customer.cart') }}" class="btn btn-outline-success">
                        Cart (
                        <span id="cartCount">{{ $cartQty }}</span>
                        ) — Ksh
                        <span id="cartTotal">{{ number_format($cartTotal, 2) }}</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
