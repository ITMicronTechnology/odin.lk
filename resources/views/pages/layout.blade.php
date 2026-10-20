@extends('layouts.app')

@section('title', $title)

@section('content')
    @include('partials.page-header', ['crumbs' => [$title => null], 'heading' => $title])

    <div class="container py-4">
        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="section-box p-2 info-nav">
                    @foreach ($pages as $slug => [$label, $pageIcon])
                        <a href="{{ route('pages.show', $slug) }}" class="{{ $slug === $page ? 'active' : '' }}"><i class="bi {{ $pageIcon }}"></i>{{ $label }}</a>
                    @endforeach
                </div>
                <div class="section-box mt-3 small">
                    <div class="fw-bold mb-2">Need help?</div>
                    <div class="mb-1"><i class="bi bi-telephone text-odin-red me-1"></i><a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}">{{ config('shop.hotline') }}</a></div>
                    <div class="mb-1"><i class="bi bi-whatsapp text-success me-1"></i>{{ config('shop.whatsapp') }}</div>
                    <div class="mb-1"><i class="bi bi-envelope text-odin-blue me-1"></i><a href="mailto:{{ config('shop.email') }}">{{ config('shop.email') }}</a></div>
                    <div class="text-muted"><i class="bi bi-clock me-1"></i>{{ config('shop.support_hours') }}</div>
                </div>
            </aside>
            <div class="col-lg-9">
                <div class="section-box info-content">
                    @yield('page')
                </div>
            </div>
        </div>
    </div>
@endsection
