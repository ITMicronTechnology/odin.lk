<?php

namespace Tests\Feature;

use App\Mail\NewOrderAlert;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class OrderEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Put the given products in the cart of a new customer (whose account email
     * differs from the checkout email) and place the order.
     *
     * @param  list<Product>  $products
     */
    private function placeOrder(array $products, string $paymentMethod = 'cod'): Order
    {
        $user = User::factory()->create(['email' => 'account@example.com']);

        foreach ($products as $product) {
            $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 2]);
        }

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Kasun Perera',
            'customer_email' => 'kasun.orders@example.com',
            'customer_phone' => '077 123 4567',
            'address' => '42 Temple Road',
            'city' => 'Nugegoda',
            'district' => 'Colombo',
            'payment_method' => $paymentMethod,
        ])->assertRedirect();

        return Order::sole();
    }

    public function test_order_confirmation_is_sent_to_the_checkout_email(): void
    {
        Mail::fake();
        $laptop = Product::factory()->create(['name' => 'HP Victus 15 Laptop', 'price' => 12000]);
        $mouse = Product::factory()->create(['name' => 'Logitech G102 Mouse', 'price' => 1500]);

        $order = $this->placeOrder([$laptop, $mouse]);

        Mail::assertSent(OrderConfirmation::class, fn (OrderConfirmation $mail): bool => $mail->hasTo('kasun.orders@example.com'));
        Mail::assertNotSent(OrderConfirmation::class, fn (OrderConfirmation $mail): bool => $mail->hasTo('account@example.com'));

        $mail = Mail::sent(OrderConfirmation::class)->sole();
        $mail->assertHasSubject("Order Confirmation – {$order->order_number} | Odin.lk")
            ->assertSeeInHtml($order->order_number)
            ->assertSeeInHtml('HP Victus 15 Laptop')
            ->assertSeeInHtml('Logitech G102 Mouse')
            ->assertSeeInHtml('Rs. 27,000.00')
            ->assertSeeInHtml('FREE')
            ->assertSeeInHtml('Thank you for shopping with')
            ->assertSeeInHtml(route('orders.track', ['order' => $order->order_number]), false)
            ->assertSeeInHtml('in cash when your order arrives')
            ->assertDontSeeInHtml(config('shop.bank.account_number'));
    }

    public function test_bank_transfer_confirmation_includes_the_bank_details(): void
    {
        Mail::fake();

        $order = $this->placeOrder([Product::factory()->create()], 'bank_transfer');

        Mail::sent(OrderConfirmation::class)->sole()
            ->assertSeeInHtml(config('shop.bank.name'))
            ->assertSeeInHtml(config('shop.bank.account_name'))
            ->assertSeeInHtml(config('shop.bank.account_number'))
            ->assertSeeInOrderInHtml(['use your order number', $order->order_number, 'as the payment reference']);
    }

    public function test_store_is_alerted_about_new_orders(): void
    {
        Mail::fake();
        config(['shop.order_alert_email' => 'orders@odin.test']);

        $order = $this->placeOrder([Product::factory()->create(['name' => 'Dell 24 Monitor'])]);

        Mail::assertSent(NewOrderAlert::class, fn (NewOrderAlert $mail): bool => $mail->hasTo('orders@odin.test'));

        Mail::sent(NewOrderAlert::class)->sole()
            ->assertSeeInHtml($order->order_number)
            ->assertSeeInHtml('Kasun Perera')
            ->assertSeeInHtml('077 123 4567')
            ->assertSeeInHtml('Dell 24 Monitor')
            ->assertSeeInHtml(route('admin.orders.show', $order), false);
    }

    public function test_order_is_placed_even_when_emails_fail(): void
    {
        Exceptions::fake();
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP server unavailable'));
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 2]);
        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Kasun Perera',
            'customer_email' => 'kasun@example.com',
            'customer_phone' => '077 123 4567',
            'address' => '42 Temple Road',
            'city' => 'Nugegoda',
            'district' => 'Colombo',
            'payment_method' => 'cod',
        ]);

        $order = Order::sole();
        $response->assertRedirect(route('checkout.success', $order));
        $this->assertSame(3, $product->fresh()->stock);
        Exceptions::assertReported(RuntimeException::class);
    }
}
