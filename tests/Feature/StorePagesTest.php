<?php

namespace Tests\Feature;

use App\Http\Controllers\PageController;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_flash_deals_offer_tiles_and_rows(): void
    {
        Product::factory()->onSale(8000)->create(['name' => 'Flash Speaker', 'price' => 10000]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Flash Deals')
            ->assertSee('Flash Speaker')
            ->assertSee('SAVE')
            ->assertSee('data-countdown', false)
            ->assertSee('Below Rs. 10,000')
            ->assertSee('Top Selling')
            ->assertSee('New Arrivals');
    }

    public function test_offer_pages_filter_products(): void
    {
        Product::factory()->create(['name' => 'Cheap Mouse', 'price' => 4000]);
        Product::factory()->create(['name' => 'Big Laptop', 'price' => 300000]);
        Product::factory()->onSale(80000)->create(['name' => 'Discounted Monitor', 'price' => 100000]);

        $this->get(route('shop.offer', 'below-10000'))
            ->assertOk()
            ->assertSee('Cheap Mouse')
            ->assertDontSee('Big Laptop');

        $this->get(route('shop.offer', 'above-10-off'))
            ->assertOk()
            ->assertSee('Discounted Monitor')
            ->assertDontSee('Cheap Mouse');

        $this->get(route('shop.offer', 'not-an-offer'))->assertNotFound();
    }

    public function test_top_selling_orders_products_by_units_sold(): void
    {
        $popular = Product::factory()->create(['name' => 'Popular Router']);
        Product::factory()->create(['name' => 'Quiet Router']);
        $order = Order::factory()->create();
        $order->items()->create([
            'product_id' => $popular->id, 'product_name' => $popular->name, 'sku' => $popular->sku,
            'price' => 10000, 'quantity' => 5, 'line_total' => 50000,
        ]);

        $this->assertSame($popular->id, Product::topSelling()->first()->id);
        $this->get(route('shop.offer', 'top-selling'))->assertOk()->assertSeeInOrder(['Popular Router', 'Quiet Router']);
    }

    public function test_brands_page_lists_brands_with_products(): void
    {
        $brand = Brand::factory()->create(['name' => 'Zebra Tech']);
        Product::factory()->for($brand)->create();
        Brand::factory()->create(['name' => 'Empty Brand']);

        $this->get(route('shop.brands'))
            ->assertOk()
            ->assertSee('Zebra Tech')
            ->assertDontSee('Empty Brand');
    }

    public function test_all_information_pages_load(): void
    {
        foreach (array_keys(PageController::PAGES) as $slug) {
            $this->get(route('pages.show', $slug))->assertOk()->assertSee(PageController::PAGES[$slug][0]);
        }
    }

    public function test_product_page_shows_easy_payment_plans_for_eligible_products(): void
    {
        $product = Product::factory()->create(['price' => 240000]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Easy Payment Plans')
            ->assertSee('Rs. 10,000.00')
            ->assertSee('ADD TO CART')
            ->assertSee('Add To Wishlist');

        $cheap = Product::factory()->create(['price' => 2000]);
        $this->get(route('products.show', $cheap))->assertOk()->assertDontSee('class="pdp-plans"', false);
    }
}
