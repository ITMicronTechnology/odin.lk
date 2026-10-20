@extends('layouts.app')

@section('title', 'Request a Quote - IT Services')

@section('content')
    @include('partials.page-header', ['crumbs' => ['Services' => route('services.index'), 'Request a Quote' => null], 'heading' => 'Request a Free Quote'])

    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="section-box">
                    <h5 class="fw-bold mb-1">Tell us what you need</h5>
                    <p class="text-muted small mb-4">Fill in the form and our team will call or email you within 1 working day with advice and a clear quotation. It's free and there's no obligation.</p>
                    @include('services.partials.form')
                </div>
            </div>
            <div class="col-lg-4">
                <div class="summary-card">
                    <h6 class="fw-bold mb-3">What happens next?</h6>
                    @foreach ([
                        ['bi-inbox', 'We review your request', 'The right specialist reads your requirements.'],
                        ['bi-telephone', 'We contact you', 'Usually within 1 working day, by phone or email.'],
                        ['bi-file-earmark-text', 'You get a quotation', 'A clear price and timeline — no hidden costs.'],
                        ['bi-rocket-takeoff', 'We get started', 'Once you approve, we plan, build and launch.'],
                    ] as [$icon, $title, $text])
                        <div class="perk mb-3"><i class="bi {{ $icon }}"></i><div><strong>{{ $title }}</strong><br><span class="text-muted">{{ $text }}</span></div></div>
                    @endforeach
                    <hr>
                    <p class="small mb-2 fw-semibold">Prefer to talk?</p>
                    <div class="d-grid gap-2">
                        <a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}" class="btn btn-light border"><i class="bi bi-telephone me-1"></i> {{ config('shop.hotline') }}</a>
                        <a href="https://wa.me/94{{ ltrim(str_replace(' ', '', config('shop.whatsapp')), '0') }}" class="btn btn-light border"><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp {{ config('shop.whatsapp') }}</a>
                        <a href="mailto:{{ config('shop.email') }}" class="btn btn-light border"><i class="bi bi-envelope me-1"></i> {{ config('shop.email') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
