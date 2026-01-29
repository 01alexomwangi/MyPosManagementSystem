 <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">

                <!-- Brand -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.Little Pos', 'POS System') }}
                </a>

                <!-- Toggler for mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar links -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">


                    <!-- Left: POS Buttons -->
                    <div class="navbar-nav me-auto">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-list"></i>

                        @auth
                           @if(auth()->user()->isAdmin())
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-user"></i> Users
                        </a>
          
                        <a href="#" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-file"></i> Reports
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-money-bill"></i> Transactions
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-users"></i> Suppliers
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-users"></i> Customers
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-user-group"></i> Incoming
                        </a>

                         <a href="{{ route('orders.index') }}" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-laptop"></i> Cashier
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-box"></i> Products
                        </a>
                        @else 

                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-laptop"></i> Cashier
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill me-2 mb-1">
                            <i class="fa fa-box"></i> Products
                        </a>

                         @endif
                      @endauth  
                    </div>

                    <!-- Right: Auth -->
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item me-2">
                                <span class="nav-link">{{ auth()->user()->name }}</span>
                            </li>
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                                </form>
                            </li>
                        @endauth
                        @guest
                            <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                            <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">Register</a></li>
                        @endguest
                    </ul>

                </div>
            </div>
        </nav>