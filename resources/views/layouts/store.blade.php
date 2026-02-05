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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto">
                @if(Session::has('customer_id'))
                    @php
                        $customer = \App\Customer::find(Session::get('customer_id'));
                    @endphp
                    <li class="nav-item">
                        <span class="nav-link">Welcome, {{ $customer->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ url('/customer/logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger nav-link">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-primary" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ url('/customer/login') }}">Login</a></li>
                            <li><a class="dropdown-item" href="{{ url('/customer/register') }}">Register</a></li>
                        </ul>
                    </li>
                @endif

                <!-- CART DROPDOWN -->
                <li class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle btn btn-outline-success" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        Cart ({{ count(Session::get('cart', [])) }})
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                        @php $cart = Session::get('cart', []); @endphp

                        @if(count($cart) > 0)
                            <ul class="list-unstyled mb-2">
                                @foreach($cart as $item)
                                    <li class="d-flex justify-content-between">
                                        <span>{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                                        <span>Ksh {{ number_format($item['total_amount'], 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-grid gap-2">
                                <form action="{{ route('customer.cart.checkout') }}" method="POST">
                               @csrf
                              <button type="submit" class="btn btn-sm btn-primary w-100">
                             Go to Checkout
                               </button>
                              </form>
                                <form action="{{ url('/cart/clear') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Clear Cart</button>
                                </form>
                            </div>
                        @else
                            <li>No items in cart.</li>
                        @endif
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>

{{-- MAIN CONTENT --}}
<main>
    @yield('content') {{-- All product grids, show pages, or checkout forms --}}
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
