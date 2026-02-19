<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">

        <!-- BRAND -->
        <a class="navbar-brand fw-bold me-4" href="{{ url('/') }}">
            Little Store
        </a>

        <!-- TOGGLER -->
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

            <!-- LEFT SIDE (Location Selector) -->
            <div class="d-flex align-items-center me-auto">
                <form method="POST" action="{{ route('store.setLocation') }}" class="me-3">
                    @csrf
                    <select name="location_id"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                            style="min-width: 180px;">

                        <option value="">Select Location</option>

                        @foreach(\App\Location::all() as $location)
                            <option value="{{ $location->id }}"
                                {{ session('selected_location') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach

                    </select>
                </form>
            </div>

            <!-- RIGHT SIDE -->
            <ul class="navbar-nav align-items-center">

                <!-- SEARCH -->
                <li class="nav-item me-4">
                    <form action="{{ route('store.index') }}" method="GET" class="d-flex">
                        <input type="text"
                               name="search"
                               class="form-control form-control-sm"
                               placeholder="Search products..."
                               value="{{ request()->get('search') }}">

                        <button class="btn btn-dark btn-sm ms-2" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </li>

                <!-- ACCOUNT -->
                @if(Session::has('customer_id'))
                    @php $customer = \App\Customer::find(Session::get('customer_id')); @endphp
                    <li class="nav-item me-2">
                        <span class="nav-link">Hi, {{ $customer->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ url('/customer/logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item dropdown me-3">
                        <a class="btn btn-outline-primary btn-sm dropdown-toggle"
                           data-bs-toggle="dropdown">
                            Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ url('/customer/login') }}">
                                    Login
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ url('/customer/register') }}">
                                    Register
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- CART -->
                <li class="nav-item">
                    <a href="{{ route('cart.view') }}"
                       class="btn btn-outline-success btn-sm d-flex align-items-center">

                        <i class="bi bi-cart3 me-2"></i>

                        Cart (
                        <span id="cartCount">{{ $cartQty }}</span>
                        ) — Ksh
                        <span id="cartTotal">{{ number_format($cartTotal, 2) }}</span>
                    </a>
                </li>

                <li class="nav-item ms-3">
                    <a href="{{ route('store.orders') }}" class="btn btn-outline-dark btn-sm">
                        My Orders
                    </a>
                </li>




            </ul>
        </div>
    </div>
</nav>
