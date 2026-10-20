@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
    @include('partials.page-header', ['crumbs' => ['Wishlist' => null], 'heading' => 'My Wishlist'])

    <div class="container py-4" data-wishlist-page>
        @if ($products->isEmpty())
            <div class="section-box empty-state">
                <i class="bi bi-heart"></i>
                <h4 class="mt-3">Your wishlist is empty</h4>
                <p class="text-muted">Tap the heart on any product to save it for later.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-odin">Explore Products</a>
            </div>
        @else
            @include('partials.product-grid', ['products' => $products])
            <div class="mt-4 d-flex justify-content-center">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
