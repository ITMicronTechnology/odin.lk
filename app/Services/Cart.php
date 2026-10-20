<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping cart keyed by product id.
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    /**
     * @var Collection<int, array{product: Product, quantity: int, line_total: float}>|null
     */
    private ?Collection $resolvedItems = null;

    public function __construct(private Session $session) {}

    /**
     * Cart lines with their products loaded. Products that were deleted or disabled are dropped.
     *
     * @return Collection<int, array{product: Product, quantity: int, line_total: float}>
     */
    public function items(): Collection
    {
        if ($this->resolvedItems !== null) {
            return $this->resolvedItems;
        }

        $quantities = $this->quantities();

        $products = Product::active()
            ->with(['primaryImage', 'brand'])
            ->whereIn('id', array_keys($quantities))
            ->get()
            ->keyBy('id');

        if ($products->count() !== count($quantities)) {
            $this->store(array_intersect_key($quantities, $products->all()));
        }

        return $this->resolvedItems = $products->map(fn (Product $product): array => [
            'product' => $product,
            'quantity' => $quantities[$product->id],
            'line_total' => $product->final_price * $quantities[$product->id],
        ])->values();
    }

    /**
     * Add a product, capping the total quantity at the available stock.
     */
    public function add(Product $product, int $quantity = 1): int
    {
        $quantities = $this->quantities();
        $quantities[$product->id] = $this->cap($product, ($quantities[$product->id] ?? 0) + $quantity);
        $this->store($quantities);

        return $quantities[$product->id];
    }

    public function update(Product $product, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($product);

            return;
        }

        $quantities = $this->quantities();
        $quantities[$product->id] = $this->cap($product, $quantity);
        $this->store($quantities);
    }

    public function remove(Product $product): void
    {
        $quantities = $this->quantities();
        unset($quantities[$product->id]);
        $this->store($quantities);
    }

    public function clear(): void
    {
        $this->store([]);
    }

    public function count(): int
    {
        return array_sum($this->quantities());
    }

    public function isEmpty(): bool
    {
        return $this->quantities() === [];
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }

    public function shippingFee(): float
    {
        $subtotal = $this->subtotal();

        if ($subtotal <= 0 || $subtotal >= config('shop.free_shipping_threshold')) {
            return 0;
        }

        return (float) config('shop.shipping_fee');
    }

    public function total(): float
    {
        return $this->subtotal() + $this->shippingFee();
    }

    /**
     * @return array<int, int>
     */
    private function quantities(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    /**
     * @param  array<int, int>  $quantities
     */
    private function store(array $quantities): void
    {
        $this->session->put(self::SESSION_KEY, $quantities);
        $this->resolvedItems = null;
    }

    private function cap(Product $product, int $quantity): int
    {
        return max(1, min($quantity, $product->stock, config('shop.max_quantity_per_item')));
    }
}
