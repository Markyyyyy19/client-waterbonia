<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="app-sidebar d-none d-lg-flex no-print">
            <a class="sidebar-brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">
                    <img src="{{ asset('images/logo3d.png') }}" alt="{{ config('app.name') }} logo">
                </span>
                <span>
                    <strong>{{ config('app.name') }}</strong>
                    <small>Laundry Shop</small>
                </span>
            </a>

            <div class="sidebar-section-label">Workspace</div>
            <nav class="sidebar-nav">
                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
                </a>
                <a class="sidebar-link {{ request()->routeIs('orders.*', 'receipt.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="bi bi-bag-check"></i><span>Orders</span>
                </a>
                <a class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                    <i class="bi bi-people"></i><span>Customers</span>
                </a>

                @if (auth()->user()->isOwner())
                    <div class="sidebar-section-label mt-4">Management</div>
                    <a class="sidebar-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <i class="bi bi-receipt"></i><span>Services &amp; Pricing</span>
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                        <i class="bi bi-box-seam"></i><span>Inventory</span>
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-bar-chart"></i><span>Reports</span>
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-person-badge"></i><span>Employees</span>
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer mt-auto">
                <div class="user-compact">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <strong class="text-truncate d-block">{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->isOwner() ? 'Owner' : 'Staff' }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Sign out" aria-label="Sign out">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar no-print">
                <button class="btn topbar-menu d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open navigation">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-brand d-lg-none">
                    <span class="brand-mark brand-mark-sm"><img src="{{ asset('images/logo3d.png') }}" alt=""></span>
                    <strong>{{ config('app.name') }}</strong>
                </div>
                <div class="topbar-spacer"></div>
                <a href="{{ route('track.index') }}" class="topbar-link" target="_blank" rel="noopener">
                    <i class="bi bi-search"></i><span class="d-none d-sm-inline">Customer tracking</span>
                </a>
            </header>

            <main class="app-content">
                <div class="content-inner">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show no-print app-alert" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show no-print app-alert" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show no-print app-alert align-items-start" role="alert">
                            <i class="bi bi-exclamation-circle-fill mt-1"></i>
                            <div>
                                <strong>Please check the form.</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start mobile-sidebar no-print" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <a class="sidebar-brand" href="{{ route('dashboard') }}" id="mobileSidebarLabel">
                <span class="brand-mark"><img src="{{ asset('images/logo3d.png') }}" alt="{{ config('app.name') }} logo"></span>
                <span><strong>{{ config('app.name') }}</strong><small>Laundry Shop</small></span>
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <div class="sidebar-section-label">Workspace</div>
            <nav class="sidebar-nav">
                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
                <a class="sidebar-link {{ request()->routeIs('orders.*', 'receipt.*') ? 'active' : '' }}" href="{{ route('orders.index') }}"><i class="bi bi-bag-check"></i><span>Orders</span></a>
                <a class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="bi bi-people"></i><span>Customers</span></a>
                @if (auth()->user()->isOwner())
                    <div class="sidebar-section-label mt-4">Management</div>
                    <a class="sidebar-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}"><i class="bi bi-receipt"></i><span>Services &amp; Pricing</span></a>
                    <a class="sidebar-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}"><i class="bi bi-box-seam"></i><span>Inventory</span></a>
                    <a class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-bar-chart"></i><span>Reports</span></a>
                    <a class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-person-badge"></i><span>Employees</span></a>
                @endif
            </nav>
            <div class="sidebar-footer mt-auto">
                <div class="user-compact">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="min-w-0"><strong class="text-truncate d-block">{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isOwner() ? 'Owner' : 'Staff' }}</small></div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="sidebar-logout" aria-label="Sign out"><i class="bi bi-box-arrow-right"></i></button></form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
