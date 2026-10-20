@extends('pages.layout')

@section('page')
<p>Shopping at Odin.lk takes just a few minutes.</p>
<ol>
    <li><strong>Find your product</strong> &mdash; use the search bar, <em>All Categories</em>, or the offers on the home page.</li>
    <li><strong>Add to cart</strong> &mdash; check the price, warranty and specifications, choose the quantity and click <em>Add to Cart</em> (or <em>Buy Now</em>).</li>
    <li><strong>Log in or create an account</strong> &mdash; it only takes a minute and lets you track orders.</li>
    <li><strong>Checkout</strong> &mdash; enter your delivery address and choose Cash on Delivery or Bank Transfer.</li>
    <li><strong>Confirmation</strong> &mdash; you receive an order number. Our team calls you to confirm delivery.</li>
    <li><strong>Delivery</strong> &mdash; follow your order any time on the <a href="{{ route('orders.track') }}">Track Order</a> page.</li>
</ol>
<p class="mb-0">Need help choosing? Call {{ config('shop.hotline') }} &mdash; we are happy to advise.</p>
@endsection
