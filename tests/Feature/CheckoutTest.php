<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function checkoutDetails(): array
    {
        return [
            'customer_name' => 'Kasun Perera',
            'customer_email' => 'kasun@example.com',
            'customer_phone' => '077 123 4567',
            'address' => '42 Temple Road',
            'city' => 'Nugegoda',
            'district' => 'Colombo',
            'payment_method' => 'cod',
        ];
    }

    public function test_guests_must_log_in_to_checkout(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('login'));
    }

    public function test_customer_can_place_an_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 12000, 'stock' => 5]);

        $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 2]);

        $response = $this->post(route('checkout.store'), $this->checkoutDetails() + ['save_address' => '1']);

        $order = Order::sole();
        $response->assertRedirect(route('checkout.success', $order));

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertEquals(24000, $order->subtotal);
        $this->assertEquals(450, $order->shipping_fee);
        $this->assertEquals(24450, $order->total);
        $this->assertSame(2, $order->items()->sole()->quantity);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertEmpty(session('cart'));
        $this->assertSame('Nugegoda', $user->fresh()->city);
    }

    public function test_order_is_rejected_when_stock_ran_out(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2]);

        $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 2]);
        $product->update(['stock' => 1]);

        $this->post(route('checkout.store'), $this->checkoutDetails())->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_customers_cannot_view_other_customers_orders(): void
    {
        $order = Order::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('account.orders.show', $order))
            ->assertForbidden();
    }

    public function test_customer_can_cancel_a_pending_order_and_stock_is_restored(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);
        $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 2]);
        $this->post(route('checkout.store'), $this->checkoutDetails());

        $order = Order::sole();
        $this->post(route('account.orders.cancel', $order))->assertRedirect();

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_shipped_orders_cannot_be_cancelled_by_the_customer(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['status' => OrderStatus::Shipped]);

        $this->actingAs($user)->post(route('account.orders.cancel', $order))->assertForbidden();
    }
}
