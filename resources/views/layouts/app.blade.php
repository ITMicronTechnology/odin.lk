<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.preloader-head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="login-url" content="{{ route('login') }}">
    <meta name="description" content="@yield('meta_description', 'Odin.lk - Sri Lanka\'s technology store for laptops, desktop PCs, printers, CCTV, power banks, Soundcore audio and computer accessories.')">
    <title>@hasSection('title')@yield('title') | @endif{{ config('shop.name') }} - {{ config('shop.tagline') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/odin-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=16" rel="stylesheet">
    <link href="{{ asset('css/store.css') }}?v=13" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @include('partials.preloader')

    {{-- Header --}}
    <header class="site-header">
        <div class="container d-flex flex-wrap align-items-center gap-2 gap-lg-4 py-2 py-lg-3">
            <button class="header-action d-lg-none px-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-label="Open menu">
                <i class="bi bi-list"></i>
            </button>

            @include('partials.logo')

            <form class="header-search d-flex" action="{{ route('shop.index') }}" method="GET" role="search" autocomplete="off">
                <input id="headerSearch" type="search" name="q" class="form-control" value="{{ request()->routeIs('shop.index') ? request('q') : '' }}"
                       placeholder="Search for products, brands, categories and more" data-suggest-url="{{ route('search.suggest') }}" aria-label="Search products">
                <button class="btn btn-odin" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                <div id="searchSuggest" class="search-suggest"></div>
            </form>

            <nav class="header-links ms-auto ms-lg-0" aria-label="Account and cart">
                <a href="{{ route('wishlist.index') }}" class="hl-item hl-fav" title="Favorites">
                    <span class="hl-icon"><i class="bi bi-heart"></i><span class="badge-count hl-badge js-wishlist-count {{ $wishlistCount ? '' : 'is-empty' }}">{{ $wishlistCount }}</span></span>
                    <span class="hl-label">Favorites</span>
                </a>

                <a href="{{ route('cart.index') }}" class="hl-item" title="My Cart">
                    <span class="hl-icon"><i class="bi bi-cart3"></i><span class="badge-count hl-badge js-cart-count {{ $cartCount ? '' : 'is-empty' }}">{{ $cartCount }}</span></span>
                    <span class="hl-label">Cart</span>
                </a>

                @auth
                    <div class="dropdown">
                        <button class="hl-item hl-account" type="button" data-bs-toggle="dropdown" data-bs-offset="0,12" aria-expanded="false" aria-label="My account">
                            <span class="hl-icon"><i class="bi bi-person"></i></span>
                            <span class="hl-label">{{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}</span>
                            <i class="bi bi-chevron-down hl-caret"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end account-menu">
                            <div class="account-menu-head">
                                <span class="account-avatar lg">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                                    <div class="small text-muted text-truncate">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            @if (auth()->user()->isAdmin())
                                <a class="account-menu-admin" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Open Admin Panel <i class="bi bi-arrow-right ms-auto"></i></a>
                            @endif
                            <div class="account-menu-grid">
                                <a href="{{ route('account.index') }}"><i class="bi bi-grid"></i>My Account</a>
                                <a href="{{ route('account.orders') }}"><i class="bi bi-bag-check"></i>My Orders</a>
                                <a href="{{ route('wishlist.index') }}"><i class="bi bi-heart"></i>Wishlist <span class="menu-count js-wishlist-count">{{ $wishlistCount }}</span></a>
                                <a href="{{ route('cart.index') }}"><i class="bi bi-bag"></i>Cart <span class="menu-count js-cart-count">{{ $cartCount }}</span></a>
                                <a href="{{ route('orders.track') }}"><i class="bi bi-truck"></i>Track Order</a>
                                <a href="{{ route('account.services') }}"><i class="bi bi-briefcase"></i>Service Requests</a>
                            </div>
                            <div class="account-menu-foot">
                                <a href="{{ route('account.profile') }}"><i class="bi bi-person-gear me-1"></i>Profile &amp; Password</a>
                                <form method="POST" action="{{ route('logout') }}" class="js-logout-form">
                                    @csrf
                                    <button class="text-danger"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hl-item" title="Login">
                        <span class="hl-icon"><i class="bi bi-person"></i></span>
                        <span class="hl-label">Login</span>
                    </a>
                    <span class="hl-divider" aria-hidden="true"></span>
                    <a href="{{ route('register') }}" class="hl-item hl-text">Register</a>
                @endauth
            </nav>
        </div>

        {{-- Category navigation --}}
        <nav class="main-nav" aria-label="Categories">
            <div class="container nav-inner px-0 px-lg-2">
                <div class="all-cats">
                    <button type="button"><i class="bi bi-list fs-5"></i> All Categories <i class="bi bi-chevron-down ms-auto small"></i></button>
                    <ul class="all-cats-menu">
                        @foreach ($navCategories as $navCategory)
                            <li>
                                <a href="{{ route('shop.category', $navCategory) }}">
                                    <i class="bi {{ $navCategory->icon ?: 'bi-tag' }} cat-icon"></i>
                                    <span class="cat-name">{{ $navCategory->name }}</span>
                                    @if ($navCategory->children->isNotEmpty())<i class="bi bi-chevron-right ms-auto small text-muted"></i>@endif
                                </a>
                                {{-- Sub-category panel: opens to the right of the whole menu, aligned to its top. --}}
                                <div class="sub">
                                    <div class="sub-head">
                                        <div>
                                            <div class="sub-title">{{ $navCategory->name }}</div>
                                            <div class="small text-muted">{{ $navCategory->children->count() ? $navCategory->children->count().' sub-categories' : 'Browse all products' }}</div>
                                        </div>
                                        <img src="{{ $navCategory->image_url }}" alt="" loading="lazy">
                                    </div>
                                    @foreach ($navCategory->children as $child)
                                        <a href="{{ route('shop.category', $child) }}"><i class="bi bi-chevron-right small me-1 text-muted"></i>{{ $child->name }}</a>
                                    @endforeach
                                    <a href="{{ route('shop.category', $navCategory) }}" class="sub-all">View all {{ $navCategory->name }} &rarr;</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="nav-links">
                    <a href="{{ route('shop.brands') }}" class="{{ request()->routeIs('shop.brands', 'shop.brand') ? 'active' : '' }}">Brands</a>
                    <div class="dropdown nav-drop">
                        <button type="button" class="nav-drop-btn {{ request()->routeIs('shop.offer', 'shop.deals') ? 'active' : '' }}" data-bs-toggle="dropdown" aria-expanded="false">TODAY'S OFFERS <i class="bi bi-chevron-down small ms-1"></i></button>
                        <ul class="dropdown-menu shadow border-0 nav-drop-menu">
                            @foreach (config('shop.offers') as $offerSlug => $offer)
                                <li><a class="dropdown-item" href="{{ route('shop.offer', $offerSlug) }}"><i class="bi {{ $offer['icon'] }} me-2" style="color: {{ $offer['from'] }}"></i>{{ $offer['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('services.index') }}" class="services-link {{ request()->routeIs('services.*') ? 'active' : '' }}"><i class="bi bi-code-square me-1"></i>IT Services</a>
                    <a href="{{ route('pages.show', 'easy-payment-plans') }}" class="{{ request()->is('easy-payment-plans') ? 'active' : '' }}">Easy Payment</a>
                    <a href="{{ route('shop.category', 'mobile-phones') }}" class="d-none d-xl-flex">Mobile Phones</a>
                    <a href="{{ route('shop.category', 'laptops') }}" class="d-none d-xl-flex">Laptops</a>
                    <a href="{{ route('shop.deals') }}" class="deals"><i class="bi bi-lightning-charge-fill me-1"></i>Hot Deals</a>
                </div>
                <div class="nav-right d-none d-lg-flex">
                    <a href="{{ route('orders.track') }}"><i class="bi bi-geo-alt-fill me-1"></i>Track your order</a>
                    <span class="nav-sep"></span>
                    <a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}"><i class="bi bi-telephone-fill me-1"></i>{{ config('shop.hotline') }}</a>
                </div>
            </div>
        </nav>
    </header>

    {{-- Mobile menu --}}
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header bg-odin-black">
            <div id="mobileMenuLabel">@include('partials.logo', ['light' => true])</div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <a href="{{ route('shop.deals') }}" class="list-group-item list-group-item-action fw-bold text-odin-red"><i class="bi bi-lightning-charge-fill me-2"></i>Hot Deals</a>
                <a href="{{ route('orders.track') }}" class="list-group-item list-group-item-action fw-semibold"><i class="bi bi-truck text-odin-blue me-2"></i>Track Order</a>
                <a href="{{ route('services.index') }}" class="list-group-item list-group-item-action fw-semibold"><i class="bi bi-code-square text-odin-blue me-2"></i>IT Services</a>
                @foreach ($navCategories as $navCategory)
                    <a href="{{ route('shop.category', $navCategory) }}" class="list-group-item list-group-item-action fw-semibold">
                        <i class="bi {{ $navCategory->icon ?: 'bi-tag' }} text-odin-blue me-2"></i>{{ $navCategory->name }}
                    </a>
                    @foreach ($navCategory->children as $child)
                        <a href="{{ route('shop.category', $child) }}" class="list-group-item list-group-item-action small ps-5">{{ $child->name }}</a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <div id="toastStack" class="toast-stack" aria-live="polite"></div>
    @foreach (['success', 'error'] as $flashType)
        @if (session($flashType))
            <span hidden data-flash="{{ session($flashType) }}" data-type="{{ $flashType }}"></span>
        @endif
    @endforeach

    <button type="button" class="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>

    {{-- Floating WhatsApp chat --}}
    <a href="https://wa.me/94{{ ltrim(str_replace(' ', '', config('shop.whatsapp')), '0') }}" class="wa-float" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    {{-- Phone bottom navigation --}}
    <nav class="mobile-bar d-lg-none" aria-label="Quick links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Home</span></a>
        <button type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"><i class="bi bi-grid"></i><span>Categories</span></button>
        <a href="{{ route('shop.deals') }}" class="{{ request()->routeIs('shop.deals', 'shop.offer') ? 'active' : '' }}"><i class="bi bi-lightning-charge"></i><span>Offers</span></a>
        <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
            <span class="mobile-bar-icon"><i class="bi bi-bag"></i><span class="badge-count js-cart-count {{ $cartCount ? '' : 'is-empty' }}">{{ $cartCount }}</span></span><span>Cart</span>
        </a>
        <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="{{ request()->routeIs('account.*', 'login') ? 'active' : '' }}"><i class="bi bi-person"></i><span>{{ auth()->check() ? 'Account' : 'Login' }}</span></a>
    </nav>

    @auth
        @include('partials.logout-modal')
    @endauth

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}?v=7"></script>
    @stack('scripts')
</body>
</html>
