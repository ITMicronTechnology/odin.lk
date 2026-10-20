<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead class="table-light">
            <tr><th>Order #</th><th>Date</th><th>Status</th><th>Payment</th><th class="text-end">Total</th><th></th></tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td class="fw-semibold">{{ $order->order_number }}</td>
                    <td class="small">{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="badge text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span></td>
                    <td class="small">{{ $order->payment_method->label() }}</td>
                    <td class="text-end fw-semibold text-nowrap">@money($order->total)</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('orders.track', ['order' => $order->order_number]) }}" class="btn btn-sm btn-light border" title="Track order"><i class="bi bi-truck"></i></a>
                        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline-odin">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
