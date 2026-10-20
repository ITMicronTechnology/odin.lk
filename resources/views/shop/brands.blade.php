@extends('layouts.app')

@section('title', 'All Brands')

@section('content')
    @include('partials.page-header', ['crumbs' => ['Brands' => null], 'heading' => 'Shop by Brand'])

    <div class="container py-4">
        <div class="d-flex flex-wrap gap-2 mb-2">
            @foreach ($brands->keys() as $letter)
                <a href="#brand-{{ $letter === '#' ? 'num' : $letter }}" class="btn btn-sm btn-light border">{{ $letter }}</a>
            @endforeach
        </div>

        @forelse ($brands as $letter => $group)
            <div class="brand-letter" id="brand-{{ $letter === '#' ? 'num' : $letter }}">{{ $letter }}</div>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                @foreach ($group as $brand)
                    <div class="col">
                        <a href="{{ route('shop.brand', $brand) }}" class="brand-card">
                            @if ($brand->logo_url)
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" loading="lazy">
                            @else
                                <span class="brand-card-name">{{ $brand->name }}</span>
                            @endif
                            <small>{{ $brand->products_count }} {{ \Illuminate\Support\Str::plural('product', $brand->products_count) }}</small>
                        </a>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="empty-state"><i class="bi bi-award"></i><p class="text-muted mt-2">No brands yet.</p></div>
        @endforelse
    </div>
@endsection
