@extends('account.layout', ['pageTitle' => 'Order #'.$order->order_number])

@section('title', 'Order '.$order->order_number)

@section('account')
    <div class="section-box mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="fw-bold mb-1">Order #{{ $order->order_number }}
                    <button type="button" class="btn btn-sm btn-link p-0 ms-1 align-baseline" data-copy="{{ $order->order_number }}" title="Copy order number"><i class="bi bi-copy"></i></button>
                </h5>
                <div class="small text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge fs-6 text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span>
                @can('cancel', $order)
                    <form method="POST" action="{{ route('account.orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Cancel Order</button>
                    </form>
                @endcan
            </div>
        </div>

        @include('partials.order-timeline')
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="section-box h-100">
                <h6 class="fw-bold mb-3">Items</h6>
                @foreach ($order->items as $item)
                    <div class="d-flex gap-3 align-items-center py-2 border-bottom cart-item">
                        <img src="{{ $item->product?->image_url ?? asset('images/no-image.svg') }}" alt="" style="width:60px;height:60px">
                        <div class="flex-grow-1 small">
                            @if ($item->product)
                                <a href="{{ route('products.show', $item->product) }}" class="text-dark fw-medium">{{ $item->product_name }}</a>
                            @else
                                <span class="fw-medium">{{ $item->product_name }}</span>
                            @endif
                            <div class="text-muted">{{ $item->quantity }} &times; @money($item->price)</div>
                        </div>
                        <div class="fw-semibold small">@money($item->line_total)</div>
                    </div>
                @endforeach
                <div class="summary-row mt-2"><span>Subtotal</span><span>@money($order->subtotal)</span></div>
                <div class="summary-row"><span>Delivery</span><span>@money($order->shipping_fee)</span></div>
                <div class="summary-row summary-total"><span>Total</span><span class="text-odin-red">@money($order->total)</span></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="section-box mb-4">
                <h6 class="fw-bold mb-2">Delivery Address</h6>
                <div class="small">
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->address }}<br>
                    {{ $order->city }}, {{ $order->district }} {{ $order->postal_code }}<br>
                    <i class="bi bi-telephone"></i> {{ $order->customer_phone }}<br>
                    <i class="bi bi-envelope"></i> {{ $order->customer_email }}
                </div>
            </div>
            <div class="section-box mb-4">
                <h6 class="fw-bold mb-2">Payment</h6>
                <div class="small">{{ $order->payment_method->label() }} &middot;
                    <span class="badge text-bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
            </div>
            <div class="section-box">
                <h6 class="fw-bold mb-2">Order History</h6>
                <ul class="list-unstyled small mb-0">
                    @foreach ($order->histories as $history)
                        <li class="mb-2"><span class="badge text-bg-{{ $history->status->color() }}">{{ $history->status->label() }}</span>
                            <span class="text-muted ms-1">{{ $history->created_at->format('d M Y, h:i A') }}</span>
                            @if ($history->note)<div class="text-muted">{{ $history->note }}</div>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
