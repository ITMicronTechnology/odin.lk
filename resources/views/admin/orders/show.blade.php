@extends('admin.layout')

@section('title', 'Order #'.$order->order_number)

@section('actions')
    <a href="{{ route('orders.track', ['order' => $order->order_number]) }}" target="_blank" class="btn btn-light btn-sm" title="What the customer sees on the Track Order page"><i class="bi bi-truck me-1"></i> Tracking page</a>
    <button onclick="window.print()" class="btn btn-light btn-sm"><i class="bi bi-printer me-1"></i> Print</button>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Items</span>
                    <span class="badge fs-6 text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            <img src="{{ $item->product?->image_url ?? asset('images/no-image.svg') }}" class="thumb" alt="">
                                            <div>
                                                @if ($item->product)
                                                    <a href="{{ route('admin.products.edit', $item->product) }}" class="text-dark fw-medium">{{ $item->product_name }}</a>
                                                @else
                                                    {{ $item->product_name }} <span class="badge text-bg-secondary">deleted</span>
                                                @endif
                                                <div class="small text-muted">{{ $item->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-nowrap">@money($item->price)</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-semibold text-nowrap">@money($item->line_total)</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">@money($order->subtotal)</td></tr>
                            <tr><td colspan="3" class="text-end">Delivery</td><td class="text-end">@money($order->shipping_fee)</td></tr>
                            @if ($order->discount > 0)<tr><td colspan="3" class="text-end">Discount</td><td class="text-end">- @money($order->discount)</td></tr>@endif
                            <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold fs-5 text-danger">@money($order->total)</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Status history</div>
                <ul class="list-group list-group-flush">
                    @foreach ($order->histories as $history)
                        <li class="list-group-item d-flex gap-3 align-items-start">
                            <span class="badge text-bg-{{ $history->status->color() }} mt-1">{{ $history->status->label() }}</span>
                            <div class="small">
                                <div class="text-muted">{{ $history->created_at->format('d M Y, h:i A') }}</div>
                                @if ($history->note)<div>{{ $history->note }}</div>@endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-header">Update status</div>
                <div class="card-body">
                    @if ($order->status === \App\Enums\OrderStatus::Cancelled)
                        <p class="text-muted small mb-0">This order is cancelled and stock has been returned.</p>
                    @else
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select mb-2">
                                @foreach (\App\Enums\OrderStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            <input name="note" class="form-control mb-2" placeholder="Note (optional, e.g. courier tracking no.)" maxlength="255">
                            <button class="btn btn-odin w-100">Update Status</button>
                            <div class="form-text">Cancelling returns the items to stock. Delivered COD orders are marked paid.</div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Payment</div>
                <div class="card-body">
                    <div class="mb-2">{{ $order->payment_method->label() }}</div>
                    <form method="POST" action="{{ route('admin.orders.payment', $order) }}" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="payment_status" class="form-select">
                            <option value="unpaid" @selected($order->payment_status === 'unpaid')>Unpaid</option>
                            <option value="paid" @selected($order->payment_status === 'paid')>Paid</option>
                        </select>
                        <button class="btn btn-odin-blue">Save</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Customer & delivery</div>
                <div class="card-body small">
                    <div class="fw-semibold fs-6">{{ $order->customer_name }}</div>
                    <div><i class="bi bi-telephone me-1"></i><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></div>
                    <div class="mb-2"><i class="bi bi-envelope me-1"></i>{{ $order->customer_email }}</div>
                    <div>{{ $order->address }}</div>
                    <div>{{ $order->city }}, {{ $order->district }} {{ $order->postal_code }}</div>
                    @if ($order->notes)
                        <div class="alert alert-warning small mt-2 mb-0"><strong>Note:</strong> {{ $order->notes }}</div>
                    @endif
                    @if ($order->user)
                        <a href="{{ route('admin.customers.show', $order->user) }}" class="btn btn-sm btn-light mt-3">View customer profile</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
