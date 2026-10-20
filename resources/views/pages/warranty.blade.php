@extends('pages.layout')

@section('page')
<p>All products sold by Odin.lk carry the official warranty shown on the product page, provided by the brand or its authorised Sri Lankan agent.</p>
<h2>How to claim warranty</h2>
<ol>
    <li>Keep your invoice and warranty card &mdash; they are required for every claim.</li>
    <li>Contact us on {{ config('shop.hotline') }} or visit our store at {{ config('shop.address') }}.</li>
    <li>We check the product and, where needed, send it to the authorised service centre.</li>
    <li>We keep you updated until the product is repaired or replaced.</li>
</ol>
<h2>Not covered</h2>
<ul>
    <li>Physical damage, liquid damage or burn marks</li>
    <li>Damage caused by power surges or incorrect installation</li>
    <li>Products repaired or opened by unauthorised people</li>
</ul>
@endsection
