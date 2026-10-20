@extends('layouts.app')

@section('title', $order ? 'Track Order #'.$order->order_number : 'Track Your Order')

@section('content')
    @include('partials.page-header', ['crumbs' => ['Track Order' => null], 'heading' => 'Track Your Order'])

    <div class="container py-4" style="max-width: 960px">
        <div class="section-box track-search mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="track-icon"><i class="bi bi-truck"></i></div>
                <div>
                    <h5 class="fw-bold mb-0">Where is my order?</h5>
                    <div class="small text-muted">Paste your order number below to see its latest status.</div>
                </div>
            </div>
            <form method="GET" action="{{ route('orders.track') }}" class="d-flex flex-column flex-sm-row gap-2">
                <label for="orderNumber" class="visually-hidden">Order number</label>
                <div class="input-group input-group-lg flex-grow-1">
                    <span class="input-group-text bg-white"><i class="bi bi-hash"></i></span>
                    <input id="orderNumber" name="order" class="form-control text-uppercase" value="{{ $orderNumber }}"
                           placeholder="e.g. ODN260927A4A6F" maxlength="30" required autocomplete="off" @unless ($order) autofocus @endunless>
                    <button type="button" class="btn btn-light border" id="pasteOrderNumber" title="Paste from clipboard"><i class="bi bi-clipboard"></i><span class="d-none d-md-inline ms-1">Paste</span></button>
                </div>
                <button class="btn btn-odin btn-lg px-4 text-nowrap"><i class="bi bi-search me-1"></i> Track Order</button>
            </form>
            <div class="small text-muted mt-2">
                <i class="bi bi-info-circle me-1"></i>Your order number is on the order confirmation page and in
                <a href="{{ route('account.orders') }}">My Account → My Orders</a>.
            </div>
            @if ($recentOrderNumbers->isNotEmpty())
                <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                    <span class="small text-muted">Your recent orders:</span>
                    @foreach ($recentOrderNumbers as $recentNumber)
                        <a href="{{ route('orders.track', ['order' => $recentNumber]) }}" class="btn btn-sm rounded-pill {{ $recentNumber === $orderNumber ? 'btn-odin-blue' : 'btn-light border' }}">{{ $recentNumber }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($orderNumber !== '' && ! $order)
            <div class="section-box empty-state">
                <i class="bi bi-search"></i>
                <h5 class="mt-3">We couldn't find order #{{ $orderNumber }}</h5>
                <p class="text-muted mb-3">Please check the order number and try again. Need help? Call <a href="tel:{{ str_replace(' ', '', config('shop.hotline')) }}">{{ config('shop.hotline') }}</a>.</p>
            </div>
        @elseif ($order)
            <div class="section-box mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <div class="small text-muted">Order number</div>
                        <h4 class="fw-bold mb-0">#{{ $order->order_number }}
                            <button type="button" class="btn btn-sm btn-link p-0 ms-1 align-baseline" data-copy="{{ $order->order_number }}" title="Copy order number"><i class="bi bi-copy"></i></button>
                        </h4>
                        <div class="small text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="text-sm-end">
                        <span class="badge fs-6 text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span>
                        <div class="small text-muted mt-1">Last update {{ ($order->histories->first()?->created_at ?? $order->updated_at)->diffForHumans() }}</div>
                    </div>
                </div>

                @include('partials.order-timeline')
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="section-box h-100">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-odin-red me-1"></i> Tracking history</h6>
                        <ul class="track-history list-unstyled mb-0">
                            @foreach ($order->histories as $history)
                                <li class="{{ $loop->first ? 'latest' : '' }}">
                                    <div class="fw-semibold">{{ $history->status->label() }}</div>
                                    <div class="small text-muted">{{ $history->created_at->format('d M Y, h:i A') }}</div>
                                    @if ($history->note)<div class="small">{{ $history->note }}</div>@endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-box h-100">
                        <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-odin-red me-1"></i> Order summary</h6>
                        @foreach ($order->items as $item)
                            <div class="d-flex justify-content-between small py-1 border-bottom">
                                <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                            </div>
                        @endforeach
                        <div class="summary-row mt-2"><span>Payment</span><span>{{ $order->payment_method->label() }}
                            <span class="badge text-bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} ms-1">{{ ucfirst($order->payment_status) }}</span></span></div>
                        <div class="summary-row summary-total"><span>Total</span><span class="text-odin-red">@money($order->total)</span></div>

                        @can('view', $order)
                            <a href="{{ route('account.orders.show', $order) }}" class="btn btn-odin-blue w-100 mt-3">View full order details</a>
                        @else
                            <p class="small text-muted mt-3 mb-0"><i class="bi bi-shield-lock me-1"></i>
                                For your privacy, delivery details are only shown after you <a href="{{ route('login') }}">log in</a>.</p>
                        @endcan
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
