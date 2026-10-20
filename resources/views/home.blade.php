@extends('layouts.app')

@section('content')
    {{-- Full-width banner slider --}}
    @if ($sliderBanners->isNotEmpty())
        <section class="hero-full">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                    @foreach ($sliderBanners as $banner)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <a href="{{ $banner->link ? url($banner->link) : '#' }}">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" @unless ($loop->first) loading="lazy" @endunless>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if ($sliderBanners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous"><span class="hero-arrow"><i class="bi bi-chevron-left"></i></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next"><span class="hero-arrow"><i class="bi bi-chevron-right"></i></span></button>
                    <div class="carousel-indicators hero-dots">
                        @foreach ($sliderBanners as $banner)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-label="{{ $banner->title }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Flash deals --}}
    @if ($flashDeals->isNotEmpty())
        @include('partials.product-row', [
            'title' => 'Flash Deals', 'icon' => 'bi-lightning-charge-fill', 'products' => $flashDeals,
            'link' => route('shop.offer', 'hot-deals'), 'flash' => true, 'endsAt' => $flashEndsAt,
        ])
    @endif

    @if ($wideBanner)
        <section class="home-block">
            <div class="container">
                <a href="{{ $wideBanner->link ? url($wideBanner->link) : '#' }}" class="promo-banner wide">
                    <img src="{{ $wideBanner->image_url }}" alt="{{ $wideBanner->title }}" loading="lazy">
                </a>
            </div>
        </section>
    @endif

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section class="home-block">
            <div class="container">
                <div class="prow-head"><h2>Shop by Category</h2><a href="{{ route('shop.index') }}" class="prow-all">ALL PRODUCTS <i class="bi bi-chevron-right"></i></a></div>
                <div class="cat-strip">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.category', $category) }}" class="cat-chip">
                            <span class="cat-chip-img"><img src="{{ $category->image_url }}" alt="" loading="lazy"></span>
                            <span>{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('partials.product-row', ['title' => 'New Arrivals', 'products' => $newArrivals, 'link' => route('shop.offer', 'new-arrivals')])

    {{-- Offer tiles --}}
    <section class="home-block">
        <div class="container">
            <div class="offer-tiles">
                @foreach ($offers as $slug => $offer)
                    <a href="{{ route('shop.offer', $slug) }}" class="offer-tile" style="--from: {{ $offer['from'] }}; --to: {{ $offer['to'] }}">
                        <span class="offer-title">{{ $offer['title'] }}</span>
                        <span class="offer-sub">{{ $offer['subtitle'] }}</span>
                        <i class="bi {{ $offer['icon'] }} offer-icon"></i>
                        <span class="offer-go"><i class="bi bi-arrow-right"></i></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if ($topSelling->isNotEmpty())
        @include('partials.product-row', ['title' => 'Top Selling', 'icon' => 'bi-trophy-fill', 'products' => $topSelling, 'link' => route('shop.offer', 'top-selling')])
    @endif

    {{-- Category rows, with promo banners and IT services in between --}}
    @foreach ($categoryRows as $row)
        @include('partials.product-row', [
            'title' => $row['category']->name,
            'products' => $row['products'],
            'link' => route('shop.category', $row['category']),
            'chips' => $row['category']->children->mapWithKeys(fn ($child) => [$child->name => route('shop.category', $child)])->all(),
        ])

        @if ($loop->iteration === 2 && $sideBanners->isNotEmpty())
            <section class="home-block">
                <div class="container">
                    <div class="row g-3">
                        @foreach ($sideBanners as $banner)
                            <div class="col-md-6">
                                <a href="{{ $banner->link ? url($banner->link) : '#' }}" class="promo-banner side">
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($loop->iteration === 4 && $serviceCategories->isNotEmpty())
            @include('partials.home-services')
        @endif
    @endforeach

    @if ($categoryRows->count() < 4 && $serviceCategories->isNotEmpty())
        @include('partials.home-services')
    @endif

    {{-- Easy payment plans --}}
    @if (config('shop.installments.enabled'))
        <section class="home-block">
            <div class="container">
                <a href="{{ route('pages.show', 'easy-payment-plans') }}" class="epp-banner">
                    <div class="epp-text">
                        <span class="epp-kicker">EASY PAYMENT PLANS</span>
                        <strong>0% interest instalments up to {{ collect(config('shop.installments.banks'))->max('months') }} months</strong>
                        <span>With leading bank credit cards &middot; or pay in {{ config('shop.installments.koko_parts') }} with KOKO</span>
                    </div>
                    <div class="epp-logos">
                        @foreach (config('shop.installments.banks') as $bank)
                            <span class="epp-logo"><img src="{{ asset('images/payment-partners/'.$bank['logo']) }}" alt="{{ $bank['name'] }}" loading="lazy"><small>{{ $bank['months'] }} months</small></span>
                        @endforeach
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- Odin picks (product spotlight) --}}
    @if ($picks->isNotEmpty())
        <section class="picks">
            <div class="container">
                <div class="picks-head">
                    <div class="brand-logo light">
                        <img src="{{ asset('images/odin-mark.svg') }}" alt="">
                        <span><span class="word">ODIN<span class="tld">.lk</span></span><span class="tag">ODIN PICKS</span></span>
                    </div>
                    <span class="picks-line"></span>
                </div>
                <div class="picks-body">
                    @foreach ($picks as $pick)
                        <div class="pick-slide {{ $loop->first ? 'active' : '' }}" data-pick="{{ $loop->index }}">
                            <div class="pick-image"><img src="{{ $pick->image_url }}" alt="{{ $pick->name }}" loading="lazy"></div>
                            <div class="pick-text">
                                <span class="pick-cat">{{ $pick->category?->name }}</span>
                                <h3>{{ $pick->name }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit(strtok($pick->description ?? '', "\n"), 330) }}</p>
                                @if ($pick->specifications)
                                    <ul>
                                        @foreach (array_slice($pick->specifications, 0, 4, true) as $label => $value)
                                            <li><b>{{ $label }}:</b> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="pick-card">
                                @include('partials.product-card', ['product' => $pick])
                                <a href="{{ route('products.show', $pick) }}" class="pick-buy">BUY NOW</a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="picks-thumbs">
                    @foreach ($picks as $pick)
                        <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-pick-to="{{ $loop->index }}">
                            <img src="{{ $pick->image_url }}" alt="" loading="lazy">
                            <span>{{ $pick->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Brand strip --}}
    @if ($brands->isNotEmpty())
        <section class="brand-strip">
            <div class="container">
                <div class="prow-slider" data-slider>
                    <button type="button" class="prow-nav prev" data-slide="-1" aria-label="Previous brands"><i class="bi bi-chevron-left"></i></button>
                    <div class="prow-track brand-track" data-track>
                        @foreach ($brands as $brand)
                            <a href="{{ route('shop.brand', $brand) }}" class="brand-logo-tile" title="Shop {{ $brand->name }}">
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                    <button type="button" class="prow-nav next" data-slide="1" aria-label="Next brands"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="text-center mt-2"><a href="{{ route('shop.brands') }}" class="prow-all">VIEW ALL BRANDS <i class="bi bi-chevron-right"></i></a></div>
            </div>
        </section>
    @endif
@endsection
