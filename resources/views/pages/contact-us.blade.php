@extends('pages.layout')

@section('page')
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="stat-card"><i class="bi bi-telephone bg-odin-red text-white"></i><div><div class="small text-muted">Hotline</div><a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}" class="fs-5 fw-bold text-dark">{{ config('shop.hotline') }}</a></div></div></div>
    <div class="col-md-6"><div class="stat-card"><i class="bi bi-whatsapp bg-success text-white"></i><div><div class="small text-muted">WhatsApp</div><a href="https://wa.me/94{{ ltrim(str_replace(' ', '', config('shop.whatsapp')), '0') }}" class="fs-5 fw-bold text-dark">{{ config('shop.whatsapp') }}</a></div></div></div>
    <div class="col-md-6"><div class="stat-card"><i class="bi bi-envelope bg-odin-blue text-white"></i><div><div class="small text-muted">Email</div><a href="mailto:{{ config('shop.email') }}" class="fw-bold text-dark">{{ config('shop.email') }}</a></div></div></div>
    <div class="col-md-6"><div class="stat-card"><i class="bi bi-clock bg-odin-black text-white"></i><div><div class="small text-muted">Opening hours</div><div class="fw-bold">{{ config('shop.support_hours') }}</div></div></div></div>
</div>
<h2>Visit our store</h2>
<p><i class="bi bi-geo-alt-fill text-odin-red me-1"></i>{{ config('shop.address') }}</p>
<div class="ratio ratio-21x9 rounded-3 overflow-hidden border mb-3">
    <iframe src="https://maps.google.com/maps?q={{ urlencode(config('shop.address')) }}&output=embed" loading="lazy" title="Map to our store" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
<p class="mb-0">For IT services, <a href="{{ route('services.request') }}">request a free quote</a>. To follow an order, use <a href="{{ route('orders.track') }}">Track your order</a>.</p>
@endsection
