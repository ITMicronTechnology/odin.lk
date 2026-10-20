<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Odin.lk Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/odin-mark.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}?v=5" rel="stylesheet">
</head>
<body>
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <img src="{{ asset('images/odin-mark.svg') }}" alt="">
            <div><div class="word">ODIN<span>.lk</span></div><small>ADMIN PANEL</small></div>
        </a>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i> Visitor Analytics</a>
            <div class="nav-label">Catalog</div>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Products</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Categories</a>
            <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><i class="bi bi-award"></i> Brands</a>
            <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}"><i class="bi bi-images"></i> Home Banners</a>
            <div class="nav-label">Sales</div>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Orders
                @if ($pendingOrders)<span class="badge rounded-pill bg-warning text-dark">{{ $pendingOrders }}</span>@endif
            </a>
            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Customers</a>
            <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i class="bi bi-star-half"></i> Reviews</a>
            <div class="nav-label">Services</div>
            <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*', 'admin.service-categories.*') ? 'active' : '' }}"><i class="bi bi-code-square"></i> IT Services</a>
            <a href="{{ route('admin.service-inquiries.index') }}" class="{{ request()->routeIs('admin.service-inquiries.*') ? 'active' : '' }}">
                <i class="bi bi-inbox"></i> Service Requests
                @if ($newServiceRequests)<span class="badge rounded-pill bg-danger">{{ $newServiceRequests }}</span>@endif
            </a>
            <div class="nav-label">Store</div>
            <a href="{{ route('home') }}" target="_blank"><i class="bi bi-shop"></i> View Store <i class="bi bi-box-arrow-up-right ms-auto small"></i></a>
        </nav>
        <div class="sidebar-foot">
            <div class="text-white fw-semibold">{{ auth()->user()->name }}</div>
            <div class="mb-2">{{ auth()->user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}" class="js-logout-form">
                @csrf
                <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i> Logout</button>
            </form>
        </div>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="btn btn-light d-lg-none" id="sidebarToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
            <h1>@yield('title', 'Dashboard')</h1>
            <div class="ms-auto d-flex gap-2">@yield('actions')</div>
        </header>

        <main class="admin-content">
            @foreach (['success' => 'success', 'error' => 'danger'] as $flash => $variant)
                @if (session($flash))
                    <div class="alert alert-{{ $variant }} alert-dismissible fade show">
                        <i class="bi {{ $flash === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-1"></i> {{ session($flash) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @include('partials.logout-modal', ['area' => 'admin'])

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(function () {
            $('#sidebarToggle, #sidebarBackdrop').on('click', function () {
                $('#adminSidebar').toggleClass('open');
                $('#sidebarBackdrop').toggleClass('show');
            });
            $(document).on('submit', 'form[data-confirm]', function () { return confirm($(this).data('confirm')); });

            let pendingLogoutForm = null;
            $(document).on('submit', '.js-logout-form', function (event) {
                event.preventDefault();
                pendingLogoutForm = this;
                bootstrap.Modal.getOrCreateInstance('#logoutModal').show();
            });
            $('#confirmLogout').on('click', function () {
                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Logging out...');
                pendingLogoutForm.submit();
            });
            $(document).on('change', '[data-autosubmit]', function () { $(this).closest('form').trigger('submit'); });
            $('input[type=file][data-preview]').on('change', function () {
                const $target = $($(this).data('preview')).empty();
                $.each(this.files, function (_, file) {
                    $('<img class="thumb me-2 mb-2" style="width:80px;height:80px" alt="">').attr('src', URL.createObjectURL(file)).appendTo($target);
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
