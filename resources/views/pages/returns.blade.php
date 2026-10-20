@extends('pages.layout')

@section('page')
<p>If there is a problem with your order, we are here to help.</p>
<h2>You can return a product if</h2>
<ul>
    <li>It was damaged when delivered (report within 24 hours)</li>
    <li>You received the wrong item or model</li>
    <li>It has a manufacturing fault within 7 days of delivery</li>
</ul>
<h2>How to return</h2>
<ol>
    <li>Call {{ config('shop.hotline') }} or WhatsApp {{ config('shop.whatsapp') }} with your order number.</li>
    <li>Keep the product with its original box, accessories and invoice.</li>
    <li>We arrange a pickup or you can bring it to our store.</li>
</ol>
<p class="mb-0">See our <a href="{{ route('pages.show', 'refund-policy') }}">Return &amp; Refund Policy</a> for refunds.</p>
@endsection
