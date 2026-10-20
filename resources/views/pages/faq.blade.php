@extends('pages.layout')

@section('page')
@php($faqs = [
    ['Are all products genuine?', 'Yes. Everything we sell is 100% genuine and brand new, bought from authorised distributors, with official local warranty.'],
    ['How long does delivery take?', 'Colombo and suburbs: 1 - 2 working days. Outstation: 2 - 4 working days by courier. You can follow your order on the Track Order page.'],
    ['How much is delivery?', 'Delivery is Rs. '.number_format(config('shop.shipping_fee')).' anywhere in Sri Lanka, and FREE for orders over Rs. '.number_format(config('shop.free_shipping_threshold')).'.'],
    ['Which payment methods can I use?', 'Cash on delivery, bank transfer, and easy payment plans with credit cards from leading banks. See the Easy Payment Plans page for details.'],
    ['Can I cancel my order?', 'Yes, you can cancel from My Account, My Orders while the order is still Pending or Confirmed. Once it is being processed, please call our hotline.'],
    ['How do I claim warranty?', 'Bring or send the product with your invoice and warranty card. See the Warranty Information page for the full process.'],
    ['Do you install CCTV and printers?', 'Yes. Our technicians can install CCTV systems, printers, networks and software. Contact us or request a quote on the IT Services page.'],
])
<div class="accordion" id="faqList">
    @foreach ($faqs as $i => [$question, $answer])
        <div class="accordion-item">
            <h3 class="accordion-header"><button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">{{ $question }}</button></h3>
            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faqList"><div class="accordion-body">{{ $answer }}</div></div>
        </div>
    @endforeach
</div>
@endsection
