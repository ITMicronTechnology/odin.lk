@extends('pages.layout')

@section('page')
<p>By using Odin.lk you agree to these terms.</p>
<h2>Orders &amp; prices</h2>
<ul>
    <li>All prices are in Sri Lankan Rupees and include applicable taxes.</li>
    <li>Prices and stock can change without notice. An order is confirmed only after our team confirms it.</li>
    <li>We may cancel an order if a product is out of stock or a price was shown in error; any payment is refunded in full.</li>
</ul>
<h2>Your account</h2>
<p>You are responsible for keeping your password safe and for all activity on your account.</p>
<h2>Warranty &amp; liability</h2>
<p>Products carry the manufacturer's warranty. Odin.lk is not liable for indirect losses arising from the use of products.</p>
<p class="mb-0">Questions? Email {{ config('shop.email') }}.</p>
@endsection
