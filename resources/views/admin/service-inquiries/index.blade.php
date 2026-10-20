@extends('admin.layout')

@section('title', 'Service Requests')

@section('content')
    <div class="status-pills d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.service-inquiries.index') }}" class="btn btn-sm {{ $currentStatus ? 'btn-light border' : 'btn-dark' }}">All <span class="badge bg-secondary ms-1">{{ $statusCounts->sum() }}</span></a>
        @foreach (\App\Enums\ServiceInquiryStatus::cases() as $status)
            <a href="{{ route('admin.service-inquiries.index', ['status' => $status->value]) }}" class="btn btn-sm {{ $currentStatus === $status ? 'btn-dark' : 'btn-light border' }}">
                {{ $status->label() }} <span class="badge text-bg-{{ $status->color() }} ms-1">{{ $statusCounts[$status->value] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">
                @if ($currentStatus)<input type="hidden" name="status" value="{{ $currentStatus->value }}">@endif
                <div class="col-md-9"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search reference, name, company, phone or email..."></div>
                <div class="col-md-3 d-grid"><button class="btn btn-odin-blue"><i class="bi bi-search me-1"></i> Search</button></div>
            </form>
            @if (request()->filled('service'))
                <div class="small mt-2">Showing requests for one service. <a href="{{ route('admin.service-inquiries.index') }}">Show all</a></div>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Reference</th><th>Customer</th><th>Service</th><th>Budget</th><th>Status</th><th>Received</th><th></th></tr></thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr class="{{ $inquiry->status === \App\Enums\ServiceInquiryStatus::New ? 'table-warning' : '' }}">
                            <td><a href="{{ route('admin.service-inquiries.show', $inquiry) }}" class="fw-semibold">{{ $inquiry->reference }}</a></td>
                            <td>{{ $inquiry->name }}<div class="small text-muted">{{ $inquiry->company ?: $inquiry->phone }}</div></td>
                            <td class="small">{{ $inquiry->service?->name ?? 'General enquiry' }}<div class="text-muted">{{ $inquiry->service?->category?->name }}</div></td>
                            <td class="small">{{ $inquiry->budget ?: '—' }}</td>
                            <td><span class="badge text-bg-{{ $inquiry->status->color() }}">{{ $inquiry->status->label() }}</span></td>
                            <td class="small text-nowrap">{{ $inquiry->created_at->diffForHumans() }}</td>
                            <td class="text-end"><a href="{{ route('admin.service-inquiries.show', $inquiry) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No service requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($inquiries->hasPages())
            <div class="card-footer bg-white">{{ $inquiries->links() }}</div>
        @endif
    </div>
@endsection
