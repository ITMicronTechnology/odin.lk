<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function orderWithItem(Product $product, int $quantity, OrderStatus $status = OrderStatus::Pending): Order
    {
        $order = Order::factory()->create(['status' => $status]);
        $order->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name, 'sku' => $product->sku,
            'price' => 10000, 'quantity' => $quantity, 'line_total' => 10000 * $quantity,
        ]);

        return $order;
    }

    public function test_customer_cancelling_a_pending_order_restores_stock_once(): void
    {
        $product = Product::factory()->create(['stock' => 4]);
        $order = $this->orderWithItem($product, 3);

        $this->actingAs($order->user)
            ->post(route('account.orders.cancel', $order))
            ->assertSessionHas('success');

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(1, $order->histories()->count());
    }

    public function test_cancelling_the_same_order_twice_restores_stock_only_once(): void
    {
        $product = Product::factory()->create(['stock' => 4]);
        $order = $this->orderWithItem($product, 3);

        $firstRequest = Order::find($order->id);
        $secondRequest = Order::find($order->id);

        $this->assertTrue($firstRequest->cancel('Cancelled by customer.', onlyIfCancellable: true));
        $this->assertFalse($secondRequest->cancel('Cancelled by customer.', onlyIfCancellable: true));

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame(1, $order->histories()->where('status', OrderStatus::Cancelled->value)->count());
        $this->assertSame(OrderStatus::Cancelled, $secondRequest->status);
    }

    public function test_admin_cannot_cancel_an_order_the_customer_already_cancelled(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['stock' => 4]);
        $order = $this->orderWithItem($product, 3);

        $this->actingAs($order->user)->post(route('account.orders.cancel', $order));

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
            ->assertSessionHas('error');

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame(1, $order->histories()->where('status', OrderStatus::Cancelled->value)->count());
    }

    public function test_customer_cannot_cancel_an_order_that_has_moved_past_confirmed(): void
    {
        $product = Product::factory()->create(['stock' => 4]);

        foreach ([OrderStatus::Processing, OrderStatus::Shipped] as $status) {
            $order = $this->orderWithItem($product, 3, $status);

            $this->actingAs($order->user)
                ->post(route('account.orders.cancel', $order))
                ->assertForbidden();

            $this->assertSame($status, $order->fresh()->status);
        }

        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_customer_cancel_rechecks_the_status_on_the_locked_row(): void
    {
        $product = Product::factory()->create(['stock' => 4]);
        $order = $this->orderWithItem($product, 3);
        $staleOrder = Order::find($order->id);

        $order->transitionTo(OrderStatus::Processing);

        $this->assertFalse($staleOrder->cancel('Cancelled by customer.', onlyIfCancellable: true));
        $this->assertSame(4, $product->fresh()->stock);
        $this->assertSame(OrderStatus::Processing, $order->fresh()->status);
    }
}
