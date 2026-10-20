@extends('admin.layout')

@section('title', $customer->name)

@section('actions')
    <a href="{{ route('admin.customers.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-2" style="width:72px;height:72px;background:var(--odin-blue);font-size:1.8rem">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-0">{{ $customer->name }}</h5>
                    <div class="text-muted small">Customer since {{ $customer->created_at->format('M Y') }}</div>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item"><i class="bi bi-envelope me-2"></i>{{ $customer->email }}</li>
                    <li class="list-group-item"><i class="bi bi-telephone me-2"></i>{{ $customer->phone ?: '—' }}</li>
                    <li class="list-group-item"><i class="bi bi-geo-alt me-2"></i>{{ collect([$customer->address, $customer->city, $customer->district, $customer->postal_code])->filter()->implode(', ') ?: 'No address saved' }}</li>
                </ul>
            </div>
            <div class="row g-3">
                <div class="col-6"><div class="card kpi"><div><div class="label">Total spent</div><div class="value fs-6">@money($totalSpent)</div></div></div></div>
                <div class="col-6"><div class="card kpi"><div><div class="label">Wishlist</div><div class="value fs-6">{{ $wishlistCount }} items</div></div></div></div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">Orders ({{ $orders->total() }})</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Order</th><th>Date</th><th>Items</th><th>Status</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a></td>
                                    <td class="small">{{ $order->created_at->format('d M Y') }}</td>
                                    <td>{{ $order->items_count }}</td>
                                    <td><span class="badge text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span></td>
                                    <td class="text-end fw-semibold">@money($order->total)</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orders->hasPages())
                    <div class="card-footer bg-white">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
