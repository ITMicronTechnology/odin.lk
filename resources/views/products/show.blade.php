@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', $product->short_description ?: $product->name)

@section('content')
    @php
        $crumbs = [];
        if ($product->category->parent) {
            $crumbs[$product->category->parent->name] = route('shop.category', $product->category->parent);
        }
        $crumbs[$product->category->name] = route('shop.category', $product->category);
        $crumbs[$product->name] = null;
        $images = $product->images;
        $plans = config('shop.installments');
        $showPlans = $product->monthly_from !== null;
        $shareUrl = urlencode(route('products.show', $product));
        $shareText = urlencode($product->name.' - Odin.lk');
    @endphp
    @include('partials.page-header', ['crumbs' => $crumbs])

    <div class="container py-4">
        <div class="pdp">
            {{-- Gallery --}}
            <div class="pdp-gallery">
                <div class="gallery-main">
                    @if ($product->isOnSale())
                        <span class="off-badge lg"><b>{{ $product->discount_percent }}%</b><small>OFF</small></span>
                    @endif
                    <img id="galleryMain" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    @if ($images->count() > 1)
                        <button type="button" class="gallery-arrow prev" data-gallery-step="-1" aria-label="Previous image"><i class="bi bi-chevron-left"></i></button>
                        <button type="button" class="gallery-arrow next" data-gallery-step="1" aria-label="Next image"><i class="bi bi-chevron-right"></i></button>
                    @endif
                </div>
                @if ($images->count() > 1)
                    <div class="gallery-thumbs">
                        @foreach ($images as $image)
                            <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-src="{{ $image->url }}" aria-label="View image {{ $loop->iteration }}">
                                <img src="{{ $image->url }}" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Main info --}}
            <div class="pdp-info">
                <h1>{{ $product->name }}</h1>
                <a href="#reviews" class="pdp-rating" data-rating-inline>
                    @include('partials.stars', ['rating' => $ratingSummary['average']])
                    <b data-inline-average @class(['d-none' => ! $ratingSummary['count']])>{{ number_format($ratingSummary['average'], 1) }}</b>
                    <span data-inline-count>{{ $ratingSummary['count'] ? '('.number_format($ratingSummary['count']).' '.\Illuminate\Support\Str::plural('review', $ratingSummary['count']).')' : 'No reviews yet - write the first one' }}</span>
                </a>
                <div class="pdp-price">
                    <strong>@money($product->final_price)</strong>
                    @if ($product->isOnSale())
                        <del>@money($product->price)</del>
                        <span class="pdp-save">SAVE @money($product->price - $product->sale_price)</span>
                    @endif
                </div>
                <div class="pdp-stock {{ $product->inStock() ? 'in' : 'out' }}">
                    <i class="bi {{ $product->inStock() ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                    {{ $product->inStock() ? ($product->stock <= 5 ? "In stock - only {$product->stock} left" : 'In stock') : 'Out of stock' }}
                </div>

                @if ($showPlans)
                    <div class="pdp-plans">
                        <div class="pdp-plans-head">Easy Payment Plans <a href="{{ route('pages.show', 'easy-payment-plans') }}">Learn more <i class="bi bi-chevron-right"></i></a></div>
                        <div class="pdp-plans-list">
                            @foreach ($plans['banks'] as $bank)
                                <div class="plan-tile" title="{{ $bank['name'] }} - {{ $bank['months'] }} months">
                                    <img src="{{ asset('images/payment-partners/'.$bank['logo']) }}" alt="{{ $bank['name'] }}">
                                    <span>Monthly <b>@money(ceil($product->final_price / $bank['months']))</b></span>
                                    <small>{{ $bank['months'] }} months</small>
                                </div>
                            @endforeach
                        </div>
                        <div class="pdp-koko">or {{ $plans['koko_parts'] }} x <b>@money(ceil($product->final_price / $plans['koko_parts']))</b> with <img src="{{ asset('images/payment-partners/koko.webp') }}" alt="KOKO"></div>
                    </div>
                @endif

                @if ($product->specifications)
                    <ul class="pdp-keyspecs">
                        @foreach (array_slice($product->specifications, 0, 5, true) as $label => $value)
                            <li><span>{{ $label }}</span>{{ $value }}</li>
                        @endforeach
                    </ul>
                @endif

                @if ($product->inStock())
                    <form method="POST" action="{{ route('cart.store', $product) }}" class="pdp-buy">
                        @csrf
                        <div class="qty-group">
                            <button type="button" data-qty-step="-1" aria-label="Decrease">&minus;</button>
                            <input id="productQty" type="number" name="quantity" value="1" min="1" max="{{ min($product->stock, config('shop.max_quantity_per_item')) }}" aria-label="Quantity">
                            <button type="button" data-qty-step="1" aria-label="Increase">+</button>
                        </div>
                        <button type="button" class="pdp-btn cart btn-add-cart" data-url="{{ route('cart.store', $product) }}" data-qty-input="#productQty"><i class="bi bi-bag-plus me-1"></i> ADD TO CART</button>
                        <button type="submit" name="buy_now" value="1" class="pdp-btn buy">BUY NOW</button>
                    </form>
                @else
                    <div class="alert alert-light border small mb-0"><i class="bi bi-bell me-1"></i> Out of stock. Call {{ config('shop.hotline') }} to check when it will be back.</div>
                @endif
            </div>

            {{-- Side panel --}}
            <aside class="pdp-side">
                <div class="pdp-brand">
                    @if ($product->brand?->logo_url)
                        <a href="{{ route('shop.brand', $product->brand) }}"><img src="{{ $product->brand->logo_url }}" alt="{{ $product->brand->name }}"></a>
                    @elseif ($product->brand)
                        <a href="{{ route('shop.brand', $product->brand) }}" class="fw-bold fs-5 text-dark">{{ $product->brand->name }}</a>
                    @else
                        <span class="fw-bold fs-5">Odin</span>
                    @endif
                </div>
                <div class="pdp-facts">
                    <div><i class="bi bi-upc-scan"></i><span><small>SKU</small>{{ $product->sku }}</span></div>
                    <div><i class="bi bi-shield-check"></i><span><small>Warranty</small>{{ $product->warranty ?: 'Manufacturer warranty' }} <a href="{{ route('pages.show', 'warranty') }}">More info</a></span></div>
                </div>
                <div class="pdp-facts">
                    <div><i class="bi bi-truck"></i><span><small>Standard Delivery</small>1 - 4 working days<em class="{{ $product->inStock() ? 'ok' : 'no' }}">{{ $product->inStock() ? 'Available' : 'Not available' }}</em></span></div>
                    <div><i class="bi bi-shop"></i><span><small>Pickup In-Store</small>Colombo 03 showroom<em class="{{ $product->inStock() ? 'ok' : 'no' }}">{{ $product->inStock() ? 'Available today' : 'Not available' }}</em></span></div>
                    <div><i class="bi bi-cash-coin"></i><span><small>Payment</small>Cash on delivery &middot; Bank transfer</span></div>
                </div>
                <button type="button" class="pdp-wish js-wishlist {{ $isWishlisted ? 'active' : '' }}" data-url="{{ route('wishlist.toggle', $product) }}">
                    <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i> Add To Wishlist
                </button>
                <div class="pdp-share">
                    <small>Share Product</small>
                    <div>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" aria-label="Share on X"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <button type="button" data-copy="{{ route('products.show', $product) }}" data-copy-label="Link" aria-label="Copy link"><i class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- Details tabs --}}
    <div class="pdp-tabs">
        <div class="container">
            <ul class="nav" role="tablist">
                <li><button class="active" data-bs-toggle="tab" data-bs-target="#tab-description" type="button">Product details</button></li>
                @if ($product->specifications)
                    <li><button data-bs-toggle="tab" data-bs-target="#tab-specs" type="button">Specification</button></li>
                @endif
                <li><button data-bs-toggle="tab" data-bs-target="#tab-delivery" type="button">Delivery &amp; Warranty</button></li>
            </ul>
        </div>
    </div>
    <div class="container py-4">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-description">
                <div class="pdp-description">{{ $product->description ?: 'No description available yet.' }}</div>
            </div>
            @if ($product->specifications)
                <div class="tab-pane fade" id="tab-specs">
                    <table class="table table-bordered spec-table mb-0" style="max-width: 820px">
                        @foreach ($product->specifications as $label => $value)
                            <tr><th>{{ $label }}</th><td>{{ $value }}</td></tr>
                        @endforeach
                    </table>
                </div>
            @endif
            <div class="tab-pane fade" id="tab-delivery">
                <ul class="mb-0">
                    <li>Colombo &amp; suburbs: delivered within 1 - 2 working days.</li>
                    <li>Outstation: delivered within 2 - 4 working days via courier.</li>
                    <li>Delivery fee @money(config('shop.shipping_fee')) &mdash; FREE for orders over @money(config('shop.free_shipping_threshold')).</li>
                    <li>All products carry the official warranty stated above. Keep your invoice and warranty card for claims.</li>
                    <li>Check your item on delivery. Report any damage within 24 hours by calling {{ config('shop.hotline') }}.</li>
                </ul>
            </div>
        </div>
    </div>

    @include('partials.product-reviews')

    @if ($related->isNotEmpty())
        @include('partials.product-row', ['title' => 'You May Also Like', 'products' => $related, 'link' => route('shop.category', $product->category)])
    @endif
@endsection
