@extends('admin.layout')

@section('title', 'Service Request '.$inquiry->reference)

@section('actions')
    <a href="{{ route('admin.service-inquiries.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
@endsection

@section('content')
    @php($whatsapp = '94'.ltrim(preg_replace('/\D/', '', $inquiry->phone), '0'))
    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $inquiry->service?->name ?? 'General enquiry' }}</span>
                    <span class="badge fs-6 text-bg-{{ $inquiry->status->color() }}">{{ $inquiry->status->label() }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3 small">
                        <div class="col-sm-4"><div class="text-muted">Service group</div><strong>{{ $inquiry->service?->category?->name ?? '—' }}</strong></div>
                        <div class="col-sm-4"><div class="text-muted">Budget</div><strong>{{ $inquiry->budget ?: 'Not given' }}</strong></div>
                        <div class="col-sm-4"><div class="text-muted">Timeline</div><strong>{{ $inquiry->timeline ?: 'Not given' }}</strong></div>
                    </div>
                    <h6 class="fw-bold">Project details</h6>
                    <div class="p-3 bg-light rounded-3" style="white-space: pre-line">{{ $inquiry->message }}</div>
                    <div class="small text-muted mt-3">Received {{ $inquiry->created_at->format('d M Y, h:i A') }} ({{ $inquiry->created_at->diffForHumans() }})</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Update status &amp; internal notes</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.service-inquiries.update', $inquiry) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="status">Status</label>
                                <select id="status" name="status" class="form-select">
                                    @foreach (\App\Enums\ServiceInquiryStatus::cases() as $status)
                                        <option value="{{ $status->value }}" @selected($inquiry->status === $status)>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">The customer sees this status in their account.</div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="admin_notes">Internal notes <span class="text-muted small">(only admins see these)</span></label>
                                <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control" placeholder="Called on Monday, sent quote Rs. 150,000...">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-odin"><i class="bi bi-check-lg me-1"></i> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-header">Customer</div>
                <div class="card-body">
                    <div class="fw-semibold fs-6">{{ $inquiry->name }}</div>
                    @if ($inquiry->company)<div class="text-muted small mb-2"><i class="bi bi-building me-1"></i>{{ $inquiry->company }}</div>@endif
                    <div class="small mb-1"><i class="bi bi-telephone me-1"></i><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></div>
                    <div class="small mb-3"><i class="bi bi-envelope me-1"></i><a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Your request '.$inquiry->reference.' - Odin.lk') }}">{{ $inquiry->email }}</a></div>
                    <div class="d-grid gap-2">
                        <a href="tel:{{ $inquiry->phone }}" class="btn btn-sm btn-light border"><i class="bi bi-telephone me-1"></i> Call</a>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-sm btn-light border"><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp</a>
                        <a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Your request '.$inquiry->reference.' - Odin.lk') }}" class="btn btn-sm btn-light border"><i class="bi bi-envelope me-1"></i> Email</a>
                    </div>
                    @if ($inquiry->user)
                        <a href="{{ route('admin.customers.show', $inquiry->user) }}" class="btn btn-sm btn-link px-0 mt-2">View customer account &rarr;</a>
                    @else
                        <div class="small text-muted mt-2">Sent as a guest (no account).</div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.service-inquiries.destroy', $inquiry) }}" data-confirm="Delete request {{ $inquiry->reference }}? This cannot be undone.">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash me-1"></i> Delete request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
