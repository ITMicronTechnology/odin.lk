@extends('admin.layout')

@section('title', 'Dashboard')

@section('actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-odin btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        @foreach ([
            ['Total Revenue', number_format((float) $stats['revenue'], 2), 'bi-cash-stack', 'var(--odin-red)', 'Rs.'],
['This Month', number_format((float) $stats['revenue_month'], 2), 'bi-graph-up-arrow', 'var(--odin-blue)', 'Rs.'],
            ['Orders', $stats['orders'].' ('.$stats['pending'].' pending)', 'bi-receipt', 'var(--odin-black)', ''],
            ['Products / Customers', $stats['products'].' / '.$stats['customers'], 'bi-box-seam', '#16a34a', ''],
        ] as [$label, $value, $icon, $color, $prefix])
            <div class="col-sm-6 col-xl-3">
                <div class="card kpi">
                    <div class="icon" style="background: {{ $color }}"><i class="bi {{ $icon }}"></i></div>
                    <div><div class="label">{{ $label }}</div><div class="value">{{ $prefix }} {{ $value }}</div></div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($newServiceRequests)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-inbox fs-5"></i>
            <div class="flex-grow-1">You have <strong>{{ $newServiceRequests }} new service {{ \Illuminate\Support\Str::plural('request', $newServiceRequests) }}</strong> waiting for a reply.</div>
            <a href="{{ route('admin.service-inquiries.index', ['status' => 'new']) }}" class="btn btn-sm btn-dark">View requests</a>
        </div>
    @endif

    <div class="card mb-4">
        <div class="visitor-strip">
            <strong><i class="bi bi-graph-up-arrow text-danger me-1"></i> Visitors</strong>
            <div>🟢 <span class="num">{{ number_format($visitors['online']) }}</span> <span class="text-muted small">online now</span></div>
            <div>📅 <span class="num">{{ number_format($visitors['daily']) }}</span> <span class="text-muted small">today</span></div>
            <div><span class="num">{{ number_format($visitors['weekly']) }}</span> <span class="text-muted small">this week</span></div>
            <div><span class="num">{{ number_format($visitors['monthly']) }}</span> <span class="text-muted small">this month</span></div>
            <div>👥 <span class="num">{{ number_format($visitors['total_visitors']) }}</span> <span class="text-muted small">all time</span></div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-odin-blue ms-auto">View analytics <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header">Sales — last 14 days</div>
                <div class="card-body"><canvas id="salesChart" height="110"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">Orders by status</div>
                <div class="card-body">
                    @foreach (\App\Enums\OrderStatus::cases() as $status)
                        @php($count = $statusCounts[$status->value] ?? 0)
                        <a href="{{ route('admin.orders.index', ['status' => $status->value]) }}" class="d-flex justify-content-between align-items-center py-2 border-bottom text-dark">
                            <span><span class="badge text-bg-{{ $status->color() }} me-2">&nbsp;</span>{{ $status->label() }}</span>
                            <strong>{{ $count }}</strong>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">Recent orders <a href="{{ route('admin.orders.index') }}" class="small">View all</a></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->customer_name }}<div class="small text-muted">{{ $order->city }}</div></td>
                                    <td class="small">{{ $order->created_at->format('d M, h:i A') }}</td>
                                    <td><span class="badge text-bg-{{ $order->status->color() }}">{{ $order->status->label() }}</span></td>
                                    <td class="text-end fw-semibold">@money($order->total)</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-header">Top selling products</div>
                <ul class="list-group list-group-flush">
                    @forelse ($topProducts as $top)
                        <li class="list-group-item d-flex justify-content-between gap-2">
                            <span class="small">{{ $top->product_name }}</span>
                            <span class="badge bg-dark align-self-center">{{ $top->sold }} sold</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No sales yet.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between">Low stock <a href="{{ route('admin.products.index', ['status' => 'low_stock']) }}" class="small">View all</a></div>
                <ul class="list-group list-group-flush">
                    @forelse ($lowStock as $product)
                        <li class="list-group-item d-flex align-items-center gap-2">
                            <img src="{{ $product->image_url }}" class="thumb" alt="">
                            <a href="{{ route('admin.products.edit', $product) }}" class="small text-dark flex-grow-1">{{ $product->name }}</a>
                            <span class="badge {{ $product->stock === 0 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $product->stock }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">All products are well stocked.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
    <script>
        (function () {
            const data = @json($chart);
            new Chart(document.getElementById('salesChart'), {
                data: {
                    labels: data.map(d => d.label),
                    datasets: [
                        { type: 'bar', label: 'Revenue (Rs.)', data: data.map(d => d.revenue), backgroundColor: 'rgba(225, 6, 0, .85)', borderRadius: 6, yAxisID: 'y' },
                        { type: 'line', label: 'Orders', data: data.map(d => d.orders), borderColor: '#0a3d91', backgroundColor: '#0a3d91', tension: .35, yAxisID: 'y1' }
                    ]
                },
                options: {
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'Rs. ' + Number(v).toLocaleString() } },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } }
                    }
                }
            });
        })();
    </script>
@endpush
