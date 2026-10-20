<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'brand', 'category.parent']);

        $related = Product::active()
            ->with(['primaryImage', 'brand'])
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('products.show', [
            'product' => $product,
            'related' => $related,
            'isWishlisted' => (bool) $request->user()?->wishlist()->whereKey($product->id)->exists(),
            'ratingSummary' => $product->ratingSummary(),
            'reviews' => $product->reviews()->with('user:id,name')->latest()->latest('id')->take(ProductReview::PER_PAGE)->get(),
            'myReview' => $request->user() ? $product->reviews()->whereBelongsTo($request->user())->first() : null,
        ]);
    }
}
