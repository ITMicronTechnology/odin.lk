@extends('admin.layout')

@section('title', 'Orders')

@section('content')
    <div class="status-pills d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ $currentStatus ? 'btn-light border' : 'btn-dark' }}">All <span class="badge bg-secondary ms-1">{{ $statusCounts->sum() }}</span></a>
        @foreach (\App\Enums\OrderStatus::cases() as $status)
            <a href="{{ route('admin.orders.index', ['status' => $status->value]) }}" class="btn btn-sm {{ $currentStatus === $status ? 'btn-dark' : 'btn-light border' }}">
                {{ $status->label() }} <span class="badge text-bg-{{ $status->color() }} ms-1">{{ $statusCounts[$status->value] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">
                @if ($currentStatus)<input type="hidden" name="status" value="{{ $currentStatus->value }}">@endif
                <div class="col-md-9"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search order number, customer name, phone or email..."></div>
                <div class="col-md-3 d-grid"><button class="btn btn-odin-blue"><i class="bi bi-search me-1"></i> Search</button></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Payment</th><th>Status</th><th class="text-end">Total</th><th></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a><div class="small text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</div></td>
                            <td>{{ $order->customer_name }}<div class="small text-muted">{{ $order->customer_phone }} · {{ $order->city }}</div></td>
                            <td>{{ $order->items_count }}</td>
                            <td class="small">{{ $order->payment_method->label() }}<br><span class="badge {{ $order->payment_status === 'paid' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($order->payment_status) }}</span></td>
                            <td><span class="badge text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span></td>
                            <td class="text-end fw-semibold text-nowrap">@money($order->total)</td>
                            <td class="text-end"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer bg-white">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
