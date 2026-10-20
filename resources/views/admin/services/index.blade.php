@extends('admin.layout')

@section('title', 'IT Services')

@section('actions')
    <a href="{{ route('services.index') }}" target="_blank" class="btn btn-light btn-sm"><i class="bi bi-eye me-1"></i> View on store</a>
    <a href="{{ route('admin.service-categories.create') }}" class="btn btn-light btn-sm border"><i class="bi bi-folder-plus me-1"></i> Add Group</a>
    <a href="{{ route('admin.services.create') }}" class="btn btn-odin btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Service</a>
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white" style="width:52px;height:52px;background:var(--odin-red);font-size:1.5rem"><i class="bi bi-file-earmark-pdf"></i></span>
                <div>
                    <div class="fw-semibold">Portfolio PDF <span class="badge {{ $portfolio['custom'] ? 'text-bg-success' : 'text-bg-warning' }} ms-1">{{ $portfolio['custom'] ? 'Your file' : 'Sample file' }}</span></div>
                    <div class="small text-muted">Customers download this from "Download our Portfolio" on the services pages &middot; {{ $portfolio['size'] }} MB &middot; updated {{ $portfolio['updated'] }}</div>
                </div>
            </div>
            <a href="{{ route('services.portfolio') }}" class="btn btn-sm btn-light border"><i class="bi bi-download me-1"></i> Download</a>
            <form method="POST" action="{{ route('admin.portfolio.update') }}" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                @csrf
                <input type="file" name="portfolio" accept="application/pdf" class="form-control form-control-sm @error('portfolio') is-invalid @enderror" required style="max-width: 240px">
                <button class="btn btn-sm btn-odin text-nowrap"><i class="bi bi-upload me-1"></i> Upload new PDF</button>
            </form>
            @if ($portfolio['custom'])
                <form method="POST" action="{{ route('admin.portfolio.destroy') }}" data-confirm="Remove your uploaded portfolio and go back to the sample PDF?">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" title="Go back to the sample PDF"><i class="bi bi-arrow-counterclockwise"></i></button>
                </form>
            @endif
        </div>
    </div>

    @forelse ($categories as $category)
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap align-items-center gap-2">
                <i class="bi {{ $category->icon ?: 'bi-folder' }} text-danger fs-5"></i>
                <span class="fw-semibold">{{ $category->name }}</span>
                <span class="badge text-bg-light border">{{ $category->services->count() }} services</span>
                @unless ($category->is_active)<span class="badge text-bg-secondary">Hidden</span>@endunless
                <div class="ms-auto d-flex gap-1">
                    <a href="{{ route('admin.service-categories.edit', $category) }}" class="btn btn-sm btn-light" title="Edit group"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.service-categories.destroy', $category) }}" data-confirm="Delete the group {{ $category->name }}?">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-light text-danger" title="Delete group"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Service</th><th>Starting price</th><th>Delivery</th><th>Requests</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($category->services as $service)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ $service->icon ?: 'bi-gear' }} fs-5 text-primary"></i>
                                        <div>
                                            <a href="{{ route('admin.services.edit', $service) }}" class="fw-semibold text-dark">{{ $service->name }}</a>
                                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($service->short_description, 70) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    @if ($service->starting_price)
                                        @money($service->starting_price) <div class="small text-muted">{{ $service->price_note }}</div>
                                    @else
                                        <span class="text-muted">Quotation</span>
                                    @endif
                                </td>
                                <td class="small">{{ $service->delivery_time }}</td>
                                <td><a href="{{ route('admin.service-inquiries.index', ['service' => $service->id]) }}" class="badge text-bg-light border text-decoration-none" title="View requests for this service">{{ $service->inquiries_count }}</a></td>
                                <td>
                                    <span class="badge {{ $service->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $service->is_active ? 'Active' : 'Hidden' }}</span>
                                    @if ($service->is_featured)<span class="badge text-bg-danger">Popular</span>@endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('services.show', $service) }}" target="_blank" class="btn btn-sm btn-light" title="View on store"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="d-inline" data-confirm="Delete {{ $service->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted small text-center py-3">No services in this group yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-center text-muted py-5">No service groups yet. <a href="{{ route('admin.service-categories.create') }}">Add the first one</a>.</div></div>
    @endforelse
@endsection
