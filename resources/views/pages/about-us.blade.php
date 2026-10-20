@extends('pages.layout')

@section('page')
<h2>Who we are</h2>
<p>Odin.lk is a Sri Lankan technology store and IT services company based in Colombo. We sell genuine laptops, mobile phones, desktop PCs, printers, CCTV systems, networking gear, storage, power banks and audio products from the world's leading brands &mdash; all with official local warranty.</p>
<p>Alongside the store, our IT services team builds websites, e-commerce stores, ERP and business management systems, and provides software installation and maintenance for businesses across the island.</p>
<h2>Why shop with Odin</h2>
<ul>
    <li>100% genuine, brand-new products with official warranty</li>
    <li>Island-wide delivery to all 25 districts, with express delivery in Colombo</li>
    <li>Cash on delivery, bank transfer and easy payment plans with leading banks</li>
    <li>Friendly after-sales support in Sinhala, Tamil and English</li>
</ul>
<h2>Our mission</h2>
<p>To make the latest technology easy and affordable for every Sri Lankan home and business &mdash; with honest advice, fair prices and support you can rely on.</p>
<p class="mb-0"><a href="{{ route('services.index') }}" class="btn btn-odin-blue btn-sm">Explore our IT services</a> <a href="{{ route('pages.show', 'contact-us') }}" class="btn btn-light border btn-sm">Contact us</a></p>
@endsection
