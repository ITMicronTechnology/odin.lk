<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        return view('wishlist.index', [
            'products' => $request->user()->wishlist()
                ->with(['primaryImage', 'brand'])
                ->where('is_active', true)
                ->latest('wishlists.created_at')
                ->paginate(12),
        ]);
    }

    public function toggle(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $changes = $request->user()->wishlist()->toggle($product->id);
        $isWishlisted = $changes['attached'] !== [];
        $message = $isWishlisted ? 'Added to your wishlist.' : 'Removed from your wishlist.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'wishlisted' => $isWishlisted,
                'count' => $request->user()->wishlist()->count(),
            ]);
        }

        return back()->with('success', $message);
    }
}
