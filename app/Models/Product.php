<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'category_id', 'brand_id', 'name', 'slug', 'sku', 'short_description', 'description',
    'specifications', 'price', 'sale_price', 'stock', 'warranty', 'is_featured', 'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasUniqueSlug;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    /**
     * @return HasOne<ProductImage, $this>
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    /**
     * @param  Builder<Product>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @param  Builder<Product>  $query
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhereHas('brand', fn (Builder $brand) => $brand->where('name', 'like', "%{$term}%"));
        });
    }

    /**
     * The price the customer actually pays.
     *
     * @return Attribute<float, never>
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn (): float => (float) ($this->isOnSale() ? $this->sale_price : $this->price));
    }

    /**
     * @return Attribute<int, never>
     */
    protected function discountPercent(): Attribute
    {
        return Attribute::get(fn (): int => $this->isOnSale()
            ? (int) round((($this->price - $this->sale_price) / $this->price) * 100)
            : 0);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->primaryImage?->url ?? asset('images/no-image.svg'));
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<ProductReview, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Average rating, review count and the 5 → 1 star breakdown, calculated live from the reviews table.
     *
     * @return array{average: float, count: int, distribution: list<array{stars: int, count: int, percent: int}>}
     */
    public function ratingSummary(): array
    {
        $counts = $this->reviews()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->map(fn ($total): int => (int) $total);

        $count = $counts->sum();
        $sum = $counts->reduce(fn (int $carry, int $total, int $rating): int => $carry + $rating * $total, 0);

        return [
            'average' => $count ? round($sum / $count, 1) : 0.0,
            'count' => $count,
            'distribution' => collect(range(ProductReview::MAX_RATING, ProductReview::MIN_RATING))
                ->map(fn (int $stars): array => [
                    'stars' => $stars,
                    'count' => $counts->get($stars, 0),
                    'percent' => $count ? (int) round($counts->get($stars, 0) * 100 / $count) : 0,
                ])
                ->all(),
        ];
    }

    /**
     * Best sellers first (by units sold on non-cancelled orders), then featured, then newest.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeTopSelling(Builder $query): void
    {
        $query->withSum(['orderItems as units_sold' => fn (Builder $items) => $items->whereHas(
            'order', fn (Builder $order) => $order->where('status', '!=', 'cancelled')
        )], 'quantity')
            ->orderByDesc('units_sold')
            ->orderByDesc('is_featured')
            ->latest('id');
    }

    /**
     * Lowest monthly instalment across the easy payment plans, or null when plans don't apply.
     *
     * @return Attribute<float|null, never>
     */
    protected function monthlyFrom(): Attribute
    {
        return Attribute::get(function (): ?float {
            $plans = config('shop.installments');
            $months = collect($plans['banks'] ?? [])->max('months');

            if (! ($plans['enabled'] ?? false) || ! $months || $this->final_price < $plans['min_price']) {
                return null;
            }

            return ceil($this->final_price / $months);
        });
    }

    /**
     * Small labels shown on product cards (like "Limited Stock").
     *
     * @return list<array{label: string, style: string}>
     */
    public function tags(): array
    {
        return array_slice(array_values(array_filter([
            ! $this->inStock() ? ['label' => 'Out of stock', 'style' => 'dark'] : null,
            $this->inStock() && $this->stock <= 5 ? ['label' => 'Limited Stock', 'style' => 'outline'] : null,
            $this->final_price >= config('shop.free_shipping_threshold') ? ['label' => 'Free Delivery', 'style' => 'blue'] : null,
        ])), 0, 2);
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }
}
