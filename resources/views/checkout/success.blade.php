@extends('layouts.app')

@section('title', 'Order Placed')

@section('content')
    <div class="container py-5">
        <div class="section-box text-center mx-auto" style="max-width: 720px">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success text-white" style="width:84px;height:84px">
                <i class="bi bi-check-lg" style="font-size:2.8rem"></i>
            </div>
            <h2 class="fw-bold">Thank you for your order!</h2>
            <p class="text-muted mb-2">Your order has been placed. We'll call you on {{ $order->customer_phone }} to confirm delivery.</p>
            <div class="d-inline-flex align-items-center gap-2 border rounded-3 px-3 py-2 mb-3 bg-light">
                <span class="small text-muted">Order number</span>
                <strong class="fs-5">{{ $order->order_number }}</strong>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-copy="{{ $order->order_number }}"><i class="bi bi-copy me-1"></i>Copy</button>
            </div>
            <p class="small text-muted">Save this number &mdash; you can use it any time on the <a href="{{ route('orders.track') }}">Track Order</a> page.</p>

            @if ($order->payment_method === \App\Enums\PaymentMethod::BankTransfer)
                <div class="alert alert-primary text-start">
                    <h6 class="fw-bold"><i class="bi bi-bank me-1"></i> Bank transfer details</h6>
                    <div>Bank: <strong>{{ config('shop.bank.name') }}</strong> ({{ config('shop.bank.branch') }})</div>
                    <div>Account name: <strong>{{ config('shop.bank.account_name') }}</strong></div>
                    <div>Account number: <strong>{{ config('shop.bank.account_number') }}</strong></div>
                    <div>Amount: <strong>@money($order->total)</strong> &middot; Reference: <strong>{{ $order->order_number }}</strong></div>
                    <div class="small mt-2">Please WhatsApp the payment slip to {{ config('shop.whatsapp') }}.</div>
                </div>
            @endif

            <div class="text-start border rounded-3 p-3 my-4">
                @foreach ($order->items as $item)
                    <div class="d-flex justify-content-between small py-1">
                        <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                        <span class="fw-semibold">@money($item->line_total)</span>
                    </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between"><span>Delivery</span><span>@money($order->shipping_fee)</span></div>
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span class="text-odin-red">@money($order->total)</span></div>
            </div>

            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('orders.track', ['order' => $order->order_number]) }}" class="btn btn-odin-dark"><i class="bi bi-truck me-1"></i> Track Order</a>
                <a href="{{ route('account.orders.show', $order) }}" class="btn btn-odin-blue"><i class="bi bi-receipt me-1"></i> View Order</a>
                <a href="{{ route('shop.index') }}" class="btn btn-odin">Continue Shopping</a>
            </div>
        </div>
    </div>
@endsection
