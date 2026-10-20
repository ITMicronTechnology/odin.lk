<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_cannot_access_the_admin_panel(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create();

        foreach ([
            route('admin.dashboard'),
            route('admin.products.index'),
            route('admin.products.create'),
            route('admin.products.edit', $product),
            route('admin.categories.index'),
            route('admin.brands.index'),
            route('admin.banners.index'),
            route('admin.orders.index'),
            route('admin.orders.show', $order),
            route('admin.customers.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_a_product_with_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'HP Victus 15 Gaming Laptop',
            'sku' => 'HP-VICTUS-15',
            'category_id' => $category->id,
            'price' => 320000,
            'sale_price' => 299000,
            'stock' => 7,
            'is_active' => '1',
            'specifications' => "Processor: Intel Core i5\nRAM: 16GB\ninvalid line",
            'images' => [UploadedFile::fake()->image('front.jpg'), UploadedFile::fake()->image('side.png')],
        ])->assertRedirect();

        $product = Product::sole();
        $this->assertSame('hp-victus-15-gaming-laptop', $product->slug);
        $this->assertSame(['Processor' => 'Intel Core i5', 'RAM' => '16GB'], $product->specifications);
        $this->assertCount(2, $product->images);
        $this->assertTrue($product->images->first()->is_primary);
        Storage::disk('public')->assertExists($product->images->first()->path);
    }

    public function test_sale_price_must_be_lower_than_price(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Bad price',
            'sku' => 'BAD-1',
            'category_id' => Category::factory()->create()->id,
            'price' => 1000,
            'sale_price' => 2000,
            'stock' => 1,
        ])->assertSessionHasErrors('sale_price');
    }

    public function test_cancelling_an_order_returns_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['stock' => 4]);
        $order = Order::factory()->create();
        $order->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku,
            'price' => 10000, 'quantity' => 3, 'line_total' => 30000,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'cancelled', 'note' => 'Customer request'])
            ->assertSessionHas('success');

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame('Customer request', $order->histories()->first()->note);
    }

    public function test_delivering_a_cash_on_delivery_order_marks_it_paid(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Shipped]);

        $this->actingAs($admin)->patch(route('admin.orders.status', $order), ['status' => 'delivered']);

        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_categories_with_products_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $product->category))
            ->assertSessionHas('error');

        $this->assertModelExists($product->category);
    }
}
