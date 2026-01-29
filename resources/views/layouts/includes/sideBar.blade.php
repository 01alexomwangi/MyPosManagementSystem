 @auth
        <div class="d-flex">
            {{-- <nav id="sidebar" class="bg-light p-3" style="width: 220px;">
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ url('/dashboard') }}"><i class="fa fa-home me-2"></i> Dashboard</a></li>
                    <li class="mb-2"><a href="{{ route('orders.index') }}"><i class="fa fa-box me-2"></i> Orders</a></li>
                    <li class="mb-2"><a href="{{ route('transactions.index') }}"><i class="fa fa-money-bill me-2"></i> Transactions</a></li>
                    <li class="mb-2"><a href="{{ route('products.index') }}"><i class="fa fa-truck me-2"></i> Products</a></li>
                </ul>
            </nav> --}}

            <!-- Main Content -->
            <div class="flex-grow-1 p-3">
                @yield('content')
            </div>
        </div>
        @endauth