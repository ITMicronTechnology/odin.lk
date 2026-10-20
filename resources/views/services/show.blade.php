@extends('layouts.app')

@section('title', $service->name.' - '.$service->category->name)
@section('meta_description', $service->short_description ?: $service->name)

@section('content')
    @include('partials.page-header', ['crumbs' => [
        'Services' => route('services.index'),
        $service->category->name => route('services.index').'#'.$service->category->slug,
        $service->name => null,
    ]])

    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="section-box mb-4">
                    <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
                        <span class="svc-icon svc-icon-lg"><i class="bi {{ $service->icon ?: 'bi-gear' }}"></i></span>
                        <div class="flex-grow-1">
                            <span class="badge text-bg-light border mb-1"><i class="bi {{ $service->category->icon }} me-1"></i>{{ $service->category->name }}</span>
                            <h1 class="h3 fw-bold mb-1">{{ $service->name }}</h1>
                            <p class="text-muted mb-0">{{ $service->short_description }}</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="price-box h-100">
                                <div class="small text-muted">{{ $service->starting_price ? 'Starting from' : 'Pricing' }}</div>
                                <div class="price-now">{{ $service->starting_price ? config('shop.currency').' '.number_format($service->starting_price, 2) : 'Free quotation' }}</div>
                                @if ($service->price_note && $service->starting_price)<div class="small text-muted">{{ $service->price_note }} &middot; final price depends on your requirements</div>@endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="price-box h-100">
                                <div class="small text-muted">Typical delivery time</div>
                                <div class="fs-4 fw-bold text-odin-blue"><i class="bi bi-clock me-1"></i>{{ $service->delivery_time ?: 'Discussed with you' }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($service->features)
                        <h5 class="fw-bold mb-3">What's included</h5>
                        <div class="row g-2 mb-4">
                            @foreach ($service->features as $feature)
                                <div class="col-md-6"><div class="svc-include"><i class="bi bi-check-circle-fill"></i>{{ $feature }}</div></div>
                            @endforeach
                        </div>
                    @endif

                    <h5 class="fw-bold mb-2">About this service</h5>
                    <div style="white-space: pre-line">{{ $service->description }}</div>
                </div>

                @if ($related->isNotEmpty())
                    <div class="section-box">
                        <div class="section-head"><h2>More in {{ $service->category->name }}</h2></div>
                        <div class="row g-3">
                            @foreach ($related as $relatedService)
                                <div class="col-md-6">@include('services.partials.card', ['service' => $relatedService])</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h5 class="fw-bold mb-1">Get a free quote</h5>
                    <p class="small text-muted mb-3">For <strong>{{ $service->name }}</strong> &mdash; we reply within 1 working day.</p>
                    @include('services.partials.form', ['fixedService' => $service, 'compact' => true])
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('services.portfolio') }}" class="btn btn-light border"><i class="bi bi-file-earmark-pdf text-danger me-1"></i> Download our Portfolio</a>
                        <a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}" class="btn btn-light border"><i class="bi bi-telephone me-1"></i> Call {{ config('shop.hotline') }}</a>
                        <a href="https://wa.me/94{{ ltrim(str_replace(' ', '', config('shop.whatsapp')), '0') }}" class="btn btn-light border"><i class="bi bi-whatsapp text-success me-1"></i> Chat on WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
