@extends('pages.layout')

@section('page')
<p>Your privacy matters to us. This policy explains what we collect and how we use it.</p>
<h2>What we collect</h2>
<ul>
    <li>Your name, phone number, email and delivery address when you register or order</li>
    <li>Your orders, wishlist and service requests</li>
    <li>Basic visit information (pages viewed, device and browser type, IP address and an anonymous cookie) to improve the website</li>
</ul>
<h2>How we use it</h2>
<ul>
    <li>To process and deliver your orders and contact you about them</li>
    <li>To provide customer support and warranty service</li>
    <li>To understand how the website is used so we can improve it</li>
</ul>
<p>We never sell your personal information. Contact {{ config('shop.email') }} to see, change or delete your data.</p>
@endsection
