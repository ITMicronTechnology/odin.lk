<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Sort options offered on listing pages.
     *
     * @var array<string, string>
     */
    public const SORT_OPTIONS = [
        'latest' => 'Newest First',
        'popular' => 'Best Selling',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'name' => 'Name: A to Z',
        'discount' => 'Biggest Discount',
    ];

    public function index(Request $request): View
    {
        $term = $request->string('q')->trim()->limit(100, '')->toString();

        return $this->listing(
            $request,
            Product::active()->when($term !== '', fn (Builder $query) => $query->search($term)),
            $term !== '' ? "Search results for \"{$term}\"" : 'All Products',
        );
    }

    public function deals(Request $request): View
    {
        return $this->listing($request, Product::active()->whereColumn('sale_price', '<', 'price'), 'Hot Deals');
    }

    /**
     * Offer pages behind the home page offer tiles (New Arrivals, Below Rs. 10,000, ...).
     */
    public function offer(Request $request, string $offer): View
    {
        $settings = config("shop.offers.{$offer}");
        abort_unless(is_array($settings), 404);

        $query = Product::active()
            ->when($offer === 'hot-deals', fn (Builder $query) => $query->whereColumn('sale_price', '<', 'price'))
            ->when(isset($settings['min_discount']), fn (Builder $query) => $query->whereRaw(
                '(price - COALESCE(sale_price, price)) * 100.0 / price >= ?', [$settings['min_discount']]
            ))
            ->when(isset($settings['max_price']), fn (Builder $query) => $query->whereRaw(
                'COALESCE(sale_price, price) <= ?', [$settings['max_price']]
            ));

        $defaultSort = match (true) {
            $offer === 'top-selling' => 'popular',
            $offer === 'new-arrivals' => 'latest',
            isset($settings['max_price']) => 'price_asc',
            default => 'discount',
        };

        return $this->listing($request, $query, $settings['title'], ['offer' => $settings + ['slug' => $offer]], $defaultSort);
    }

    /**
     * All brands, A to Z.
     */
    public function brands(): View
    {
        return view('shop.brands', [
            'brands' => Brand::active()
                ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
                ->orderBy('name')
                ->get()
                ->filter(fn (Brand $brand): bool => $brand->products_count > 0)
                ->groupBy(fn (Brand $brand): string => ctype_alpha($letter = mb_strtoupper(mb_substr($brand->name, 0, 1))) ? $letter : '#'),
        ]);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->is_active, 404);

        $category->load(['parent', 'children' => fn ($query) => $query->active()]);

        return $this->listing(
            $request,
            Product::active()->whereIn('category_id', $category->selfAndChildIds()),
            $category->name,
            ['category' => $category],
        );
    }

    public function brand(Request $request, Brand $brand): View
    {
        abort_unless($brand->is_active, 404);

        return $this->listing($request, Product::active()->where('brand_id', $brand->id), $brand->name, ['brand' => $brand]);
    }

    /**
     * Live search suggestions for the header search box.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = $request->string('q')->trim()->limit(100, '')->toString();

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->search($term)
            ->with('primaryImage')
            ->orderByDesc('is_featured')
            ->take(6)
            ->get()
            ->map(fn (Product $product): array => [
                'name' => $product->name,
                'url' => route('products.show', $product),
                'image' => $product->image_url,
                'price' => config('shop.currency').' '.number_format($product->final_price, 2),
            ]);

        return response()->json($products);
    }

    /**
     * Apply the shared filters, sorting and pagination to a product listing.
     *
     * @param  Builder<Product>  $baseQuery
     * @param  array<string, mixed>  $context
     */
    private function listing(Request $request, Builder $baseQuery, string $title, array $context = [], string $defaultSort = 'latest'): View
    {
        $selectedBrands = array_values(array_filter(array_map('intval', $request->array('brands'))));
        $minPrice = $request->filled('min_price') ? max(0, $request->integer('min_price')) : null;
        $maxPrice = $request->filled('max_price') ? max(0, $request->integer('max_price')) : null;
        $inStockOnly = $request->boolean('in_stock');
        $sort = array_key_exists($request->query('sort'), self::SORT_OPTIONS) ? $request->query('sort') : $defaultSort;

        $availableBrands = Brand::active()
            ->whereIn('id', (clone $baseQuery)->select('brand_id'))
            ->orderBy('name')
            ->get();

        $products = (clone $baseQuery)
            ->with(['primaryImage', 'brand'])
            ->when($selectedBrands, fn (Builder $query) => $query->whereIn('brand_id', $selectedBrands))
            ->when($minPrice !== null, fn (Builder $query) => $query->whereRaw('COALESCE(sale_price, price) >= ?', [$minPrice]))
            ->when($maxPrice !== null, fn (Builder $query) => $query->whereRaw('COALESCE(sale_price, price) <= ?', [$maxPrice]))
            ->when($inStockOnly, fn (Builder $query) => $query->where('stock', '>', 0))
            ->tap(fn (Builder $query) => match ($sort) {
                'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
                'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
                'name' => $query->orderBy('name'),
                'discount' => $query->orderByRaw('(price - COALESCE(sale_price, price)) * 1.0 / price DESC'),
                'popular' => $query->topSelling(),
                default => $query->latest('id'),
            })
            ->paginate(16)
            ->withQueryString();

        return view('shop.index', [
            'title' => $title,
            'products' => $products,
            'availableBrands' => $availableBrands,
            'rootCategories' => Category::active()->roots()->orderBy('sort_order')->get(),
            'filters' => [
                'brands' => $selectedBrands,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'in_stock' => $inStockOnly,
                'sort' => $sort,
                'q' => $request->string('q')->trim()->toString(),
            ],
            'sortOptions' => self::SORT_OPTIONS,
            'category' => null,
            'brand' => null,
            'offer' => null,
            ...$context,
        ]);
    }
}
