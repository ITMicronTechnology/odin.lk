<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_added_to_cart_via_ajax(): void
    {
        $product = Product::factory()->onSale(9000)->create();

        $this->postJson(route('cart.store', $product), ['quantity' => 2])
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('subtotal', 'Rs. 18,000.00');

        $this->assertSame([$product->id => 2], session('cart'));
    }

    public function test_out_of_stock_products_cannot_be_added(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $this->postJson(route('cart.store', $product))->assertStatus(422);

        $this->assertEmpty(session('cart', []));
    }

    public function test_quantity_is_capped_at_available_stock(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->postJson(route('cart.store', $product), ['quantity' => 2]);
        $this->postJson(route('cart.store', $product), ['quantity' => 2])->assertJsonPath('count', 3);
    }

    public function test_free_delivery_applies_above_threshold(): void
    {
        $cheap = Product::factory()->create(['price' => 1000]);
        $expensive = Product::factory()->create(['price' => 50000]);

        $this->postJson(route('cart.store', $cheap))->assertJsonPath('shipping', 'Rs. 450.00');
        $this->postJson(route('cart.store', $expensive))->assertJsonPath('shipping', 'FREE');
    }

    public function test_cart_items_can_be_updated_and_removed(): void
    {
        $product = Product::factory()->create();
        $this->postJson(route('cart.store', $product));

        $this->patchJson(route('cart.update', $product), ['quantity' => 4])->assertJsonPath('count', 4);
        $this->deleteJson(route('cart.destroy', $product))->assertJsonPath('count', 0);
    }
}
