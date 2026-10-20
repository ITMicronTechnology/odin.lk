@extends('layouts.app')

@section('content')
    @include('partials.page-header', ['crumbs' => ['My Account' => route('account.index'), $pageTitle => null], 'heading' => $pageTitle])

    <div class="container py-4">
        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="account-nav">
                    <div class="p-3 border-bottom d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-odin-blue text-white d-flex align-items-center justify-content-center fw-bold" style="width:44px;height:44px">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="small lh-sm">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <div class="text-muted">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('account.index') }}" class="{{ request()->routeIs('account.index') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('account.orders') }}" class="{{ request()->routeIs('account.orders*') ? 'active' : '' }}"><i class="bi bi-bag-check"></i> My Orders</a>
                    <a href="{{ route('orders.track') }}"><i class="bi bi-truck"></i> Track Order</a>
                    <a href="{{ route('account.services') }}" class="{{ request()->routeIs('account.services') ? 'active' : '' }}"><i class="bi bi-briefcase"></i> Service Requests</a>
                    <a href="{{ route('wishlist.index') }}"><i class="bi bi-heart"></i> Wishlist</a>
                    <a href="{{ route('account.profile') }}" class="{{ request()->routeIs('account.profile') ? 'active' : '' }}"><i class="bi bi-person-gear"></i> Profile & Password</a>
                    <form method="POST" action="{{ route('logout') }}" class="js-logout-form">
                        @csrf
                        <button class="text-danger border-0"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </div>
            </aside>
            <div class="col-lg-9">
                @yield('account')
            </div>
        </div>
    </div>
@endsection
