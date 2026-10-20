@extends('admin.layout')

@section('title', 'Product Reviews')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body">
                <div class="small text-muted">Total reviews</div>
                <div class="fs-4 fw-bold">{{ number_format($totalReviews) }}</div>
            </div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body">
                <div class="small text-muted">Average rating</div>
                <div class="fs-4 fw-bold">{{ number_format($averageRating, 1) }} <span class="fs-6 text-muted">/ 5</span></div>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2">
                <div class="col-md-7"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search product, customer or review text..."></div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">All ratings</option>
                        @for ($star = 5; $star >= 1; $star--)
                            <option value="{{ $star }}" @selected((int) request('rating') === $star)>{{ $star }} {{ \Illuminate\Support\Str::plural('star', $star) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-grid"><button class="btn btn-odin-blue"><i class="bi bi-search me-1"></i> Filter</button></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Product</th><th>Customer</th><th>Rating</th><th style="min-width: 280px">Review</th><th>Date</th><th></th></tr></thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td><a href="{{ route('products.show', $review->product) }}#reviews" target="_blank" class="fw-semibold text-dark">{{ $review->product->name }}</a></td>
                            <td class="small"><a href="{{ route('admin.customers.show', $review->user) }}" class="text-dark">{{ $review->user->name }}</a><div class="text-muted">{{ $review->user->email }}</div></td>
                            <td class="text-nowrap">@include('partials.stars', ['rating' => $review->rating])</td>
                            <td class="small" style="white-space: pre-line">{{ \Illuminate\Support\Str::limit($review->comment, 220) }}</td>
                            <td class="small text-nowrap">{{ $review->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Delete review"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No reviews found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reviews->hasPages())
            <div class="card-footer bg-white">{{ $reviews->links() }}</div>
        @endif
    </div>
@endsection
