<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
use App\Http\Resources\ProductReviewResource;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductReviewController extends Controller
{
    /**
     * GET /api/products/{product}/reviews - newest reviews first, paginated.
     */
    public function index(Product $product): AnonymousResourceCollection
    {
        abort_unless($product->is_active, 404);

        $reviews = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->latest('id')
            ->paginate(ProductReview::PER_PAGE);

        return ProductReviewResource::collection($reviews);
    }

    /**
     * GET /api/products/{product}/rating - average, total and 5 → 1 star breakdown.
     */
    public function rating(Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        return response()->json(['data' => $product->ratingSummary()]);
    }

    /**
     * POST /api/products/{product}/reviews - create the signed-in customer's review, or update it if they already wrote one.
     */
    public function store(StoreProductReviewRequest $request, Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        // The reviewer always comes from the session; user_id is not fillable, so it can never be taken from the request.
        $review = $product->reviews()->whereBelongsTo($request->user())->firstOrNew();
        $review->fill($request->safe()->only(['rating', 'comment']));
        $review->user()->associate($request->user());
        $review->save();

        $created = $review->wasRecentlyCreated;

        return response()->json([
            'message' => $created ? 'Thank you! Your review has been posted.' : 'Your review has been updated.',
            'data' => ProductReviewResource::make($review->load('user:id,name'))->resolve($request),
            'summary' => $product->ratingSummary(),
        ], $created ? 201 : 200);
    }
}
