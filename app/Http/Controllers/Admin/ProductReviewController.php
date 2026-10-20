<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->trim()->toString();
        $rating = $request->integer('rating');

        $reviews = ProductReview::with(['product:id,name,slug', 'user:id,name,email'])
            ->when($rating >= ProductReview::MIN_RATING && $rating <= ProductReview::MAX_RATING, fn ($query) => $query->where('rating', $rating))
            ->when($term !== '', fn ($query) => $query->where(function ($query) use ($term) {
                $query->where('comment', 'like', "%{$term}%")
                    ->orWhereHas('product', fn ($query) => $query->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'totalReviews' => ProductReview::count(),
            'averageRating' => round((float) ProductReview::avg('rating'), 1),
        ]);
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
