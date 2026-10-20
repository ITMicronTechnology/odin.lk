<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::active()->get()->groupBy('position');

        // One sliding product row per main category (the ones ticked "show on home" come first).
        $categoryRows = Category::active()->roots()
            ->with(['children' => fn ($query) => $query->active()])
            ->orderByDesc('show_on_home')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $category): array => [
                'category' => $category,
                'products' => $this->products()
                    ->whereIn('category_id', $category->selfAndChildIds())
                    ->orderByDesc('is_featured')
                    ->latest('id')
                    ->take(12)
                    ->get(),
            ])
            ->filter(fn (array $row): bool => $row['products']->isNotEmpty())
            ->values();

        return view('home', [
            'sliderBanners' => $banners->get('slider', collect()),
            'sideBanners' => $banners->get('side', collect())->take(2),
            'wideBanner' => $banners->get('wide', collect())->first(),
            'categories' => Category::active()->roots()->orderBy('sort_order')->get(),
            'flashDeals' => $this->products()
                ->whereColumn('sale_price', '<', 'price')
                ->where('stock', '>', 0)
                ->orderByRaw('(price - sale_price) / price DESC')
                ->take(12)
                ->get(),
            // Flash deals count down to the end of the week (Sunday midnight).
            'flashEndsAt' => now()->endOfWeek(),
            'newArrivals' => $this->products()->latest('id')->take(12)->get(),
            'topSelling' => $this->products()->topSelling()->take(12)->get(),
            'categoryRows' => $categoryRows,
            'picks' => $this->products()->with('category')->where('is_featured', true)->latest('id')->take(6)->get(),
            'offers' => config('shop.offers'),
            'brands' => Brand::active()->withRealLogo()->orderBy('name')->get(),
            'serviceCategories' => ServiceCategory::active()
                ->withCount(['services' => fn ($query) => $query->active()])
                ->orderBy('sort_order')
                ->get()
                ->where('services_count', '>', 0),
        ]);
    }

    /**
     * @return Builder<Product>
     */
    private function products(): Builder
    {
        return Product::active()->with(['primaryImage', 'brand']);
    }
}
