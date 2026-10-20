@extends('admin.layout')

@section('title', 'Home Banners')

@section('actions')
    <a href="{{ route('admin.banners.create') }}" class="btn btn-odin btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Banner</a>
@endsection

@section('content')
    @foreach (\App\Models\Banner::POSITIONS as $position => $label)
        <div class="card mb-4">
            <div class="card-header">{{ $label }}</div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse ($banners->get($position, collect()) as $banner)
                        <div class="{{ $position === 'side' ? 'col-md-6 col-xl-4' : 'col-md-6' }}">
                            <div class="border rounded-3 overflow-hidden {{ $banner->is_active ? '' : 'opacity-50' }}">
                                <img src="{{ $banner->image_url }}" alt="" class="w-100 d-block">
                                <div class="p-2 d-flex align-items-center gap-2 bg-light">
                                    <div class="flex-grow-1 small">
                                        <div class="fw-semibold">{{ $banner->title }}</div>
                                        <div class="text-muted">Order {{ $banner->sort_order }} · {{ $banner->link ?: 'No link' }} @unless ($banner->is_active) · Hidden @endunless</div>
                                    </div>
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" data-confirm="Delete this banner?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted small">No banners in this position.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
@endsection
