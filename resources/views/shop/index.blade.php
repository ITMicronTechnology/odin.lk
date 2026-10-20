@extends('layouts.app')

@section('title', $title)

@section('content')
    @php
        $crumbs = [];
        if ($category?->parent) {
            $crumbs[$category->parent->name] = route('shop.category', $category->parent);
        }
        if ($brand) {
            $crumbs['Brands'] = route('shop.brands');
        }
        if ($offer) {
            $crumbs["Today's Offers"] = null;
        }
        $crumbs[$title] = null;
    @endphp
    @include('partials.page-header', ['crumbs' => $crumbs, 'heading' => $title])

    <div class="container py-4">
        <div class="row g-4">
            {{-- Filters --}}
            <aside class="col-lg-3">
                <button class="btn btn-odin-dark w-100 d-lg-none mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#filters">
                    <i class="bi bi-funnel me-1"></i> Filters
                </button>
                <form id="filters" class="collapse d-lg-block" method="GET">
                    @if ($filters['q'] !== '')
                        <input type="hidden" name="q" value="{{ $filters['q'] }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ $filters['sort'] }}">

                    <div class="filter-box">
                        <h6>Categories</h6>
                        <div class="filter-body">
                            @if ($category)
                                @if ($category->parent)
                                    <a href="{{ route('shop.category', $category->parent) }}" class="filter-cat"><span><i class="bi bi-arrow-left me-1"></i>{{ $category->parent->name }}</span></a>
                                @endif
                                <a href="{{ route('shop.category', $category) }}" class="filter-cat active"><span>{{ $category->name }}</span></a>
                                @foreach ($category->children as $child)
                                    <a href="{{ route('shop.category', $child) }}" class="filter-cat ps-3"><span>{{ $child->name }}</span><i class="bi bi-chevron-right small"></i></a>
                                @endforeach
                            @else
                                @foreach ($rootCategories as $root)
                                    <a href="{{ route('shop.category', $root) }}" class="filter-cat"><span>{{ $root->name }}</span><i class="bi bi-chevron-right small"></i></a>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    @if ($availableBrands->isNotEmpty() && ! $brand)
                        <div class="filter-box">
                            <h6>Brands</h6>
                            <div class="filter-body">
                                @foreach ($availableBrands as $availableBrand)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $availableBrand->id }}" id="brand-{{ $availableBrand->id }}"
                                               @checked(in_array($availableBrand->id, $filters['brands'], true)) data-autosubmit>
                                        <label class="form-check-label" for="brand-{{ $availableBrand->id }}">{{ $availableBrand->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="filter-box">
                        <h6>Price Range (Rs.)</h6>
                        <div class="filter-body">
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" min="0" value="{{ $filters['min_price'] }}">
                                <span>-</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" min="0" value="{{ $filters['max_price'] }}">
                            </div>
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" role="switch" name="in_stock" value="1" id="inStock" @checked($filters['in_stock']) data-autosubmit>
                                <label class="form-check-label" for="inStock">In stock only</label>
                            </div>
                            <button class="btn btn-odin btn-sm w-100 mt-3">Apply Filters</button>
                            <a href="{{ url()->current() }}{{ $filters['q'] !== '' ? '?q='.urlencode($filters['q']) : '' }}" class="btn btn-link btn-sm w-100 text-muted">Clear all</a>
                        </div>
                    </div>
                </form>
            </aside>

            {{-- Results --}}
            <div class="col-lg-9">
                @if ($offer)
                    <div class="offer-hero" style="--from: {{ $offer['from'] }}; --to: {{ $offer['to'] }}">
                        <h2>{{ $offer['title'] }}</h2>
                        <p>{{ $offer['subtitle'] }} &middot; {{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }}</p>
                        <i class="bi {{ $offer['icon'] }}"></i>
                    </div>
                    <div class="offer-links">
                        @foreach (config('shop.offers') as $offerSlug => $otherOffer)
                            <a href="{{ route('shop.offer', $offerSlug) }}" class="{{ $offerSlug === $offer['slug'] ? 'active' : '' }}"><i class="bi {{ $otherOffer['icon'] }} me-1"></i>{{ $otherOffer['title'] }}</a>
                        @endforeach
                    </div>
                @endif
                <div class="toolbar d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="small text-muted">
                        @if ($products->total())
                            Showing <strong class="text-dark">{{ $products->firstItem() }}-{{ $products->lastItem() }}</strong> of <strong class="text-dark">{{ $products->total() }}</strong> products
                        @else
                            No products found
                        @endif
                    </div>
                    <form method="GET" class="d-flex align-items-center gap-2">
                        @foreach (request()->except(['sort', 'page']) as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $item)<input type="hidden" name="{{ $key }}[]" value="{{ $item }}">@endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label for="sort" class="small text-muted text-nowrap">Sort by</label>
                        <select id="sort" name="sort" class="form-select form-select-sm" data-autosubmit>
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="section-box empty-state">
                        <i class="bi bi-search"></i>
                        <h5 class="mt-3">No products match your search</h5>
                        <p class="text-muted">Try different keywords or remove some filters.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-odin">Browse all products</a>
                    </div>
                @else
                    @include('partials.product-grid', ['products' => $products, 'wide' => false])
                    <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
