<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_deals_and_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Laptops']);
        Product::factory()->for($category)->onSale(9000)->create(['name' => 'Discounted Laptop']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Laptops')
            ->assertSee('Discounted Laptop')
            ->assertSee('Hot Deals');
    }

    public function test_top_brands_section_only_shows_brands_with_real_logos(): void
    {
        Brand::factory()->create(['name' => 'Real Logo Brand', 'logo' => 'brands/real-logo-brand.webp']);
        Brand::factory()->create(['name' => 'Sample Logo Brand', 'logo' => 'brands/sample-logo-brand.svg']);
        Brand::factory()->create(['name' => 'No Logo Brand', 'logo' => null]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('alt="Real Logo Brand"', false)
            ->assertDontSee('alt="Sample Logo Brand"', false)
            ->assertDontSee('alt="No Logo Brand"', false);
    }

    public function test_category_page_includes_products_from_sub_categories(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);
        Product::factory()->for($child)->create(['name' => 'Child Category Product']);
        Product::factory()->create(['name' => 'Unrelated Product']);

        $this->get(route('shop.category', $parent))
            ->assertOk()
            ->assertSee('Child Category Product')
            ->assertDontSee('Unrelated Product');
    }

    public function test_listing_filters_by_brand_and_price(): void
    {
        $brand = Brand::factory()->create();
        Product::factory()->for($brand)->create(['name' => 'Cheap Branded', 'price' => 5000]);
        Product::factory()->for($brand)->create(['name' => 'Pricey Branded', 'price' => 90000]);
        Product::factory()->create(['name' => 'Other Brand', 'price' => 5000]);

        $this->get(route('shop.index', ['brands' => [$brand->id], 'max_price' => 10000]))
            ->assertOk()
            ->assertSee('Cheap Branded')
            ->assertDontSee('Pricey Branded')
            ->assertDontSee('Other Brand');
    }

    public function test_search_and_suggestions_find_products(): void
    {
        Product::factory()->create(['name' => 'Soundcore Motion Speaker']);
        Product::factory()->create(['name' => 'Office Printer']);

        $this->get(route('shop.index', ['q' => 'soundcore']))
            ->assertOk()
            ->assertSee('Soundcore Motion Speaker')
            ->assertDontSee('Office Printer');

        $this->getJson(route('search.suggest', ['q' => 'motion']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Soundcore Motion Speaker');
    }

    public function test_hidden_products_are_not_viewable(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_product_page_shows_price_and_specifications(): void
    {
        $product = Product::factory()->onSale(8500)->create(['specifications' => ['Processor' => 'Intel Core i7']]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Rs. 8,500.00')
            ->assertSee('Rs. 10,000.00')
            ->assertSee('Intel Core i7');
    }
}
