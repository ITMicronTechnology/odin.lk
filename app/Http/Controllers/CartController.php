<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index(): View
    {
        return view('cart.index', ['cart' => $this->cart]);
    }

    public function store(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.config('shop.max_quantity_per_item')],
        ]);

        if (! $product->is_active || ! $product->inStock()) {
            return $this->respond($request, 'Sorry, this product is currently out of stock.', 422);
        }

        $this->cart->add($product, $validated['quantity'] ?? 1);

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.create');
        }

        return $this->respond($request, "{$product->name} was added to your cart.");
    }

    public function update(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.config('shop.max_quantity_per_item')],
        ]);

        $this->cart->update($product, $validated['quantity']);

        return $this->respond($request, 'Cart updated.');
    }

    public function destroy(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->cart->remove($product);

        return $this->respond($request, 'Item removed from your cart.');
    }

    /**
     * Return JSON for AJAX calls (with fresh totals) or redirect back for normal form posts.
     */
    private function respond(Request $request, string $message, int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            $currency = config('shop.currency').' ';

            return response()->json([
                'message' => $message,
                'count' => $this->cart->count(),
                'lines' => $this->cart->items()->mapWithKeys(fn (array $line): array => [
                    $line['product']->id => [
                        'quantity' => $line['quantity'],
                        'line_total' => $currency.number_format($line['line_total'], 2),
                    ],
                ]),
                'subtotal' => $currency.number_format($this->cart->subtotal(), 2),
                'shipping' => $this->cart->shippingFee() > 0 ? $currency.number_format($this->cart->shippingFee(), 2) : 'FREE',
                'total' => $currency.number_format($this->cart->total(), 2),
            ], $status);
        }

        return back()->with($status === 200 ? 'success' : 'error', $message);
    }
}
