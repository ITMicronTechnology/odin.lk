@extends('account.layout', ['pageTitle' => 'My Account'])

@section('title', 'My Account')

@section('account')
    <div class="section-box mb-4">
        <h4 class="fw-bold mb-1">Hello, {{ $user->name }} 👋</h4>
        <p class="text-muted mb-0">From your account dashboard you can view recent orders, manage your delivery address and change your password.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="stat-card"><i class="bi bi-bag-check bg-odin-red text-white"></i><div><div class="small text-muted">Total Orders</div><div class="fs-4 fw-bold">{{ $orderCount }}</div></div></div></div>
        <div class="col-md-4"><div class="stat-card"><i class="bi bi-truck bg-odin-blue text-white"></i><div><div class="small text-muted">Active Orders</div><div class="fs-4 fw-bold">{{ $pendingCount }}</div></div></div></div>
        <div class="col-md-4"><div class="stat-card"><i class="bi bi-heart bg-odin-black text-white"></i><div><div class="small text-muted">Wishlist Items</div><div class="fs-4 fw-bold">{{ $wishlistCount }}</div></div></div></div>
    </div>

    <div class="section-box">
        <div class="section-head">
            <h2>Recent Orders</h2>
            <a href="{{ route('account.orders') }}" class="view-all">View all</a>
        </div>
        @if ($recentOrders->isEmpty())
            <div class="empty-state py-4">
                <i class="bi bi-bag"></i>
                <p class="text-muted mt-2">You haven't placed any orders yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-odin">Start Shopping</a>
            </div>
        @else
            @include('account.partials.orders-table', ['orders' => $recentOrders])
        @endif
    </div>
@endsection
