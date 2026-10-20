@extends('admin.layout')

@section('title', 'Categories')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-odin btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Category</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Category</th><th>Slug</th><th>Products</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        @foreach ([$category, ...$category->children] as $row)
                            <tr class="{{ $row->parent_id ? '' : 'table-light' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2 {{ $row->parent_id ? 'ps-4' : '' }}">
                                        @if ($row->parent_id)
                                            <i class="bi bi-arrow-return-right text-muted"></i>
                                        @else
                                            <img src="{{ $row->image_url }}" class="thumb" alt="">
                                        @endif
                                        <i class="bi {{ $row->icon ?: 'bi-tag' }} text-primary"></i>
                                        <span class="{{ $row->parent_id ? '' : 'fw-semibold' }}">{{ $row->name }}</span>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $row->slug }}</td>
                                <td>{{ $row->products_count }}</td>
                                <td>{{ $row->sort_order }}</td>
                                <td>
                                    <span class="badge {{ $row->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $row->is_active ? 'Active' : 'Hidden' }}</span>
                                    @if ($row->show_on_home)<span class="badge text-bg-primary">Home</span>@endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('admin.categories.edit', $row) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $row) }}" class="d-inline" data-confirm="Delete {{ $row->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
