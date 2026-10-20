@extends('admin.layout')

@section('title', 'Customers')

@section('content')
    <div class="card">
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">
                <div class="col-md-9"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, email or phone..."></div>
                <div class="col-md-3 d-grid"><button class="btn btn-odin-blue"><i class="bi bi-search me-1"></i> Search</button></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Customer</th><th>Phone</th><th>Location</th><th>Orders</th><th>Total spent</th><th>Joined</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><a href="{{ route('admin.customers.show', $customer) }}" class="fw-semibold text-dark">{{ $customer->name }}</a><div class="small text-muted">{{ $customer->email }}</div></td>
                            <td class="small">{{ $customer->phone ?: '—' }}</td>
                            <td class="small">{{ collect([$customer->city, $customer->district])->filter()->implode(', ') ?: '—' }}</td>
                            <td>{{ $customer->orders_count }}</td>
                            <td class="fw-semibold text-nowrap">@money($customer->total_spent ?? 0)</td>
                            <td class="small">{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="text-end"><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="card-footer bg-white">{{ $customers->links() }}</div>
        @endif
    </div>
@endsection
