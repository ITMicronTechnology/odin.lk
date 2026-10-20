@extends('admin.layout')

@section('title', 'Products')

@section('actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-odin btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name, SKU or brand...">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select" data-autosubmit>
                        <option value="">All categories</option>
                        @foreach ($categories as $root)
                            <option value="{{ $root->id }}" @selected(request('category') == $root->id)>{{ $root->name }}</option>
                            @foreach ($root->children as $child)
                                <option value="{{ $child->id }}" @selected(request('category') == $child->id)>&nbsp;&nbsp;— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" data-autosubmit>
                        <option value="">All status</option>
                        @foreach (['active' => 'Active', 'inactive' => 'Hidden', 'featured' => 'Featured', 'low_stock' => 'Low stock (≤5)'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid"><button class="btn btn-odin-blue"><i class="bi bi-search me-1"></i> Filter</button></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="{{ $product->image_url }}" class="thumb" alt="">
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="fw-semibold text-dark">{{ $product->name }}</a>
                                        <div class="small text-muted">{{ $product->sku }} · {{ $product->brand?->name ?? 'No brand' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $product->category->name }}</td>
                            <td class="text-nowrap">
                                <div class="fw-semibold">@money($product->final_price)</div>
                                @if ($product->isOnSale())<div class="small text-muted text-decoration-line-through">@money($product->price)</div>@endif
                            </td>
                            <td><span class="badge {{ $product->stock === 0 ? 'bg-danger' : ($product->stock <= 5 ? 'bg-warning text-dark' : 'bg-success') }}">{{ $product->stock }}</span></td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $product->is_active ? 'Active' : 'Hidden' }}</span>
                                @if ($product->is_featured)<span class="badge text-bg-primary">Featured</span>@endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('products.show', $product) }}" target="_blank" class="btn btn-sm btn-light" title="View on store"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" data-confirm="Delete {{ $product->name }}? This cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="card-footer bg-white">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
