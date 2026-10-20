@extends('layouts.app')

@section('title', 'IT Services - Websites, ERP & Business Software')
@section('meta_description', 'Odin IT services in Sri Lanka: business and e-commerce websites, ERP software, HR, payroll, inventory and CRM systems, software installation and maintenance.')

@section('content')
    {{-- Hero --}}
    <section class="svc-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="svc-eyebrow"><i class="bi bi-stars me-1"></i> ODIN IT SERVICES</span>
                    <h1>Websites, ERP &amp; business software for Sri Lankan businesses</h1>
                    <p class="lead">From your first website to a complete ERP &mdash; we design, build, install and support the technology that runs your business.</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('services.request') }}" class="btn btn-odin btn-lg"><i class="bi bi-send me-1"></i> Request a Free Quote</a>
                        <a href="{{ route('services.portfolio') }}" class="btn btn-outline-light btn-lg"><i class="bi bi-file-earmark-pdf me-1"></i> Download our Portfolio</a>
                    </div>
                    <div class="small mt-3 opacity-75"><i class="bi bi-telephone me-1"></i> Prefer to talk? Call <a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}" class="text-white fw-semibold">{{ config('shop.hotline') }}</a></div>
                </div>
                <div class="col-lg-5">
                    <div class="svc-hero-grid">
                        @foreach ($categories as $category)
                            <a href="#{{ $category->slug }}" class="svc-hero-tile">
                                <i class="bi {{ $category->icon ?: 'bi-gear' }}"></i>
                                <span>{{ $category->name }}</span>
                                <small>{{ $category->services->count() }} solutions</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Why us --}}
    <section class="pt-3">
        <div class="container">
            <div class="services row g-0 shadow-sm">
                <div class="col-6 col-lg-3 service"><i class="bi bi-chat-dots"></i><div><h6>Free Consultation</h6><p>We listen before we quote</p></div></div>
                <div class="col-6 col-lg-3 service"><i class="bi bi-geo-alt"></i><div><h6>Local Team</h6><p>Colombo based, islandwide</p></div></div>
                <div class="col-6 col-lg-3 service"><i class="bi bi-cash-coin"></i><div><h6>Clear Pricing</h6><p>No hidden costs</p></div></div>
                <div class="col-6 col-lg-3 service"><i class="bi bi-life-preserver"></i><div><h6>After-sales Support</h6><p>We stay with you</p></div></div>
            </div>
        </div>
    </section>

    {{-- Category shortcuts --}}
    <nav class="svc-tabs" aria-label="Service areas">
        <div class="container d-flex gap-2 overflow-auto py-2">
            @foreach ($categories as $category)
                <a href="#{{ $category->slug }}" class="btn btn-sm btn-light border rounded-pill text-nowrap"><i class="bi {{ $category->icon }} me-1"></i>{{ $category->name }}</a>
            @endforeach
        </div>
    </nav>

    @foreach ($categories as $category)
        <section class="section {{ $loop->first ? 'pt-3' : 'pt-0' }}" id="{{ $category->slug }}">
            <div class="container">
                <div class="section-box">
                    <div class="svc-cat-head">
                        <span class="svc-cat-icon"><i class="bi {{ $category->icon ?: 'bi-gear' }}"></i></span>
                        <div>
                            <h2>{{ $loop->iteration }}. {{ $category->name }}</h2>
                            <p class="mb-0"><strong>{{ $category->tagline }}</strong> &mdash; {{ $category->description }}</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        @foreach ($category->services as $service)
                            <div class="col-md-6 col-xl-4">@include('services.partials.card')</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    {{-- How we work --}}
    <section class="section pt-0">
        <div class="container">
            <div class="section-box">
                <div class="section-head"><h2>How we work</h2></div>
                <div class="row g-3">
                    @foreach ([
                        ['bi-chat-square-text', 'Talk to us', 'Tell us what your business needs. The first consultation is free.'],
                        ['bi-clipboard-check', 'Plan & quote', 'We suggest the right solution and send a clear, fixed quotation.'],
                        ['bi-laptop', 'Build & set up', 'We build, install and test everything, and train your staff.'],
                        ['bi-headset', 'Support', 'Maintenance, updates and help whenever you need it.'],
                    ] as $index => [$icon, $title, $text])
                        <div class="col-6 col-lg-3">
                            <div class="svc-step">
                                <span class="svc-step-no">{{ $index + 1 }}</span>
                                <i class="bi {{ $icon }}"></i>
                                <h6>{{ $title }}</h6>
                                <p>{{ $text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="pb-2">
        <div class="container">
            <div class="help-cta d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h3 class="fw-bold mb-1">Have a project in mind?</h3>
                    <p class="mb-0 opacity-75">Tell us about it and get a free, no-obligation quotation within 1 working day.</p>
                </div>
                <div class="d-flex gap-2 position-relative" style="z-index: 1">
                    <a href="{{ route('services.request') }}" class="btn btn-odin btn-lg"><i class="bi bi-send me-2"></i>Request a Quote</a>
                    <a href="{{ route('services.portfolio') }}" class="btn btn-outline-light btn-lg"><i class="bi bi-file-earmark-pdf me-2"></i>Portfolio (PDF)</a>
                    <a href="https://wa.me/94{{ ltrim(str_replace(' ', '', config('shop.whatsapp')), '0') }}" class="btn btn-light btn-lg"><i class="bi bi-whatsapp me-2 text-success"></i>WhatsApp</a>
                </div>
            </div>
        </div>
    </section>
@endsection
