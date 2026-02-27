<!-- Modern Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm mb-4" 
     style="background: linear-gradient(135deg, #1e3c72, #2a5298);">

    <div class="container-fluid">

        <!-- Brand -->
        <a class="navbar-brand fw-bold text-white" href="{{ url('/') }}">
            <i class="fa fa-store me-2"></i> Little POS
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" 
                data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">

            <!-- Center Menu -->
            <div class="mx-auto d-flex flex-wrap justify-content-center gap-2">

                <!-- Receipts Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill px-4 shadow-sm dropdown-toggle"
                            type="button" data-bs-toggle="dropdown">
                        <i class="fa fa-receipt me-1"></i> Receipts
                    </button>

                    <ul class="dropdown-menu shadow border-0 rounded-4 p-2">
                        <li><a class="dropdown-item rounded-3" href="{{ route('reports.receipts') }}">All Receipts</a></li>
                        <li><a class="dropdown-item rounded-3" href="{{ route('reports.receipts', ['from' => \Carbon\Carbon::today()->toDateString()]) }}">Today</a></li>
                        <li><a class="dropdown-item rounded-3" href="{{ route('reports.receipts', ['from' => \Carbon\Carbon::now()->startOfWeek()->toDateString(), 'to' => \Carbon\Carbon::now()->endOfWeek()->toDateString()]) }}">This Week</a></li>
                        <li><a class="dropdown-item rounded-3" href="{{ route('reports.receipts', ['from' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'to' => \Carbon\Carbon::now()->endOfMonth()->toDateString()]) }}">This Month</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item rounded-3 text-primary" onclick="window.print()">
                                <i class="fa fa-print me-1"></i> Print All
                            </button>
                        </li>
                    </ul>
                </div>

                @auth

                    @if(auth()->user()->isAdmin())

                        <a href="{{ route('users.index') }}"
                           class="btn btn-light rounded-pill px-4 shadow-sm {{ request()->routeIs('users.index') ? 'border border-2 border-dark' : '' }}">
                           <i class="fa fa-user me-1"></i> Users
                        </a>

                        <a href="{{ route('admin.logs') }}"
                           class="btn btn-light rounded-pill px-4 shadow-sm {{ request()->routeIs('admin.logs') ? 'border border-2 border-dark' : '' }}">
                           <i class="fa fa-file-alt me-1"></i> Logs
                        </a>

                    @endif

                    <a href="{{ route('products.index') }}"
                       class="btn btn-light rounded-pill px-4 shadow-sm {{ request()->routeIs('products.index') ? 'border border-2 border-dark' : '' }}">
                       <i class="fa fa-box me-1"></i> Products
                    </a>

                    <a href="{{ route('orders.index') }}"
                       class="btn btn-light rounded-pill px-4 shadow-sm {{ request()->routeIs('orders.index') ? 'border border-2 border-dark' : '' }}">
                       <i class="fa fa-cash-register me-1"></i> Orders
                    </a>

                    <a href="{{ route('reports.custom') }}"
                       class="btn btn-light rounded-pill px-4 shadow-sm {{ request()->routeIs('reports.custom') ? 'border border-2 border-dark' : '' }}">
                       <i class="fa fa-chart-line me-1"></i> My Sales
                    </a>

                @endauth
            </div>

            <!-- Right User Section -->
            <ul class="navbar-nav ms-auto align-items-center">

                @auth
                    <li class="nav-item me-3">
                        <span class="text-white fw-semibold">
                            <i class="fa fa-user-circle me-1"></i> {{ auth()->user()->name }}
                        </span>
                    </li>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-light btn-sm rounded-pill px-3">
                                Logout
                            </button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-light rounded-pill px-4">
                            Login
                        </a>
                    </li>
                @endguest

            </ul>

        </div>
    </div>
</nav>