<!-- Navbar -->
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">

        <!-- Left: Brand -->
        <a class="navbar-brand fw-bold me-auto" href="{{ url('/') }}">
            Little POS
        </a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar collapse -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Center: POS buttons + Receipts dropdown -->
            <div class="mx-auto d-flex flex-wrap justify-content-center mb-2">

                <!-- Receipts Dropdown -->
                <div class="dropdown me-2 mb-2">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="receiptsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-receipt"></i> Receipts
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="receiptsDropdown">
                        <li><a class="dropdown-item" href="{{ route('reports.receipts') }}">All Receipts</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.receipts', ['from' => \Carbon\Carbon::today()->toDateString()]) }}">Today</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.receipts', ['from' => \Carbon\Carbon::now()->startOfWeek()->toDateString(), 'to' => \Carbon\Carbon::now()->endOfWeek()->toDateString()]) }}">This Week</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.receipts', ['from' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'to' => \Carbon\Carbon::now()->endOfMonth()->toDateString()]) }}">This Month</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item" onclick="window.print()"><i class="fa fa-print"></i> Print All</button></li>
    
                    </ul>
                </div>

                <!-- Admin-only buttons -->
                @auth
                    @if(auth()->user()->isAdmin())
                       <a href="{{ route('users.index') }}" 
                       class="btn {{ request()->routeIs('users.index') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill me-2 mb-2">
                       <i class="fa fa-user"></i> Users
                   </a>

                  <a href="{{ route('reports.custom') }}" 
                    class="btn {{ request()->routeIs('reports.custom') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill me-2 mb-2">
                   <i class="fa fa-chart-line"></i> Reports
                    </a>
                    @endif

                    <!-- Common POS buttons -->
                   
           <a href="{{ route('products.index') }}"
            class="btn {{ request()->routeIs('products.index') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill me-2 mb-2">
            <i class="fa fa-box"></i> Products
           </a>

          <a href="{{ route('sales.index') }}"
          class="btn {{ request()->routeIs('sales.index') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill me-2 mb-2">
          <i class="fa fa-cash-register"></i> Sales
          </a>

          <a href="{{ route('cashier.pending') }}"
             class="btn {{ request()->routeIs('cashier.pending') ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill me-2 mb-2">
          <i class="fa fa-clock"></i> Pending Sales
         </a>

         <a href="{{ route('reports.custom') }}"
            class="btn {{ request()->routeIs('reports.custom') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill me-2 mb-2">
         <i class="fa fa-chart-line"></i> My Sales
         </a>
                @endauth
            </div>

            <!-- Right: User info / auth -->
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                @auth
                    <li class="nav-item me-2">
                        <span class="nav-link fw-bold">{{ auth()->user()->name }}</span>
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
