@extends('pages.layout')

@section('page')
<p>Buy now and pay in easy monthly instalments with credit cards from leading Sri Lankan banks.</p>
<div class="row g-3 mb-4">
    @foreach (config('shop.installments.banks') as $bank)
        <div class="col-6 col-md-4">
            <div class="border rounded-3 p-3 text-center h-100">
                <img src="{{ asset('images/payment-partners/'.$bank['logo']) }}" alt="{{ $bank['name'] }}" style="height:40px;max-width:100%;object-fit:contain">
                <div class="fw-bold mt-2">Up to {{ $bank['months'] }} months</div>
                <div class="small text-muted">{{ $bank['name'] }} credit cards</div>
            </div>
        </div>
    @endforeach
</div>
<h2>How it works</h2>
<ul>
    <li>Easy payment plans apply to products above Rs. {{ number_format(config('shop.installments.min_price')) }}.</li>
    <li>The <em>Monthly</em> amount shown on products is the lowest instalment available.</li>
    <li>Pay in {{ config('shop.installments.koko_parts') }} interest-free parts with KOKO.</li>
    <li>Call {{ config('shop.hotline') }} to place an instalment order &mdash; bank terms and conditions apply.</li>
</ul>
@endsection
