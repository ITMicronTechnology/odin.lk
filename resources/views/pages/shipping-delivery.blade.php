@extends('pages.layout')

@section('page')
<h2>Delivery areas &amp; times</h2>
<ul>
    <li><strong>Colombo &amp; suburbs:</strong> 1 - 2 working days (express delivery)</li>
    <li><strong>Outstation (all 25 districts):</strong> 2 - 4 working days by courier</li>
    <li>Orders placed after 3.00 PM, on Sundays or public holidays are dispatched the next working day.</li>
</ul>
<h2>Delivery charges</h2>
<p>Rs. {{ number_format(config('shop.shipping_fee')) }} per order, and <strong>FREE delivery on orders over Rs. {{ number_format(config('shop.free_shipping_threshold')) }}</strong>.</p>
<h2>Receiving your order</h2>
<p>Please check the package in front of the delivery person. If anything is damaged or missing, report it within 24 hours by calling {{ config('shop.hotline') }}.</p>
@endsection
