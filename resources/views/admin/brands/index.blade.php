@extends('admin.layout')

@section('title', 'Brands')

@section('actions')
    <a href="{{ route('admin.brands.create') }}" class="btn btn-odin btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Brand</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Logo</th><th>Name</th><th>Products</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td>
                                @if ($brand->logo_url)
                                    <img src="{{ $brand->logo_url }}" alt="" style="height:40px;width:92px;object-fit:contain" class="border rounded bg-white">
                                @else
                                    <span class="text-muted small">No logo</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $brand->name }}<div class="small text-muted fw-normal">{{ $brand->slug }}</div></td>
                            <td>{{ $brand->products_count }}</td>
                            <td><span class="badge {{ $brand->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $brand->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="d-inline" data-confirm="Delete {{ $brand->name }}? Its products will become unbranded.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No brands yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($brands->hasPages())
            <div class="card-footer bg-white">{{ $brands->links() }}</div>
        @endif
    </div>
@endsection
