<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_page_shows_the_search_form(): void
    {
        $this->get(route('orders.track'))
            ->assertOk()
            ->assertSee('Where is my order?')
            ->assertSee('Track Order');
    }

    public function test_guest_can_track_an_order_by_its_number(): void
    {
        $order = Order::factory()->create(['order_number' => 'ODN260927ABCDE', 'status' => OrderStatus::Shipped]);
        $order->histories()->create(['status' => 'shipped', 'note' => 'Courier tracking no. PX123']);

        $this->get(route('orders.track', ['order' => ' #odn260927abcde ']))
            ->assertOk()
            ->assertSee('#ODN260927ABCDE')
            ->assertSee('Shipped')
            ->assertSee('Courier tracking no. PX123')
            ->assertSee(OrderStatus::Shipped->description());
    }

    public function test_tracking_does_not_reveal_customer_contact_details(): void
    {
        $order = Order::factory()->create([
            'customer_phone' => '0719998887',
            'customer_email' => 'private@example.com',
            'address' => '99 Secret Lane',
        ]);

        $this->get(route('orders.track', ['order' => $order->order_number]))
            ->assertOk()
            ->assertDontSee('0719998887')
            ->assertDontSee('private@example.com')
            ->assertDontSee('99 Secret Lane');
    }

    public function test_unknown_order_number_shows_a_helpful_message(): void
    {
        $this->get(route('orders.track', ['order' => 'ODN000000XXXXX']))
            ->assertOk()
            ->assertSee("We couldn't find order #ODN000000XXXXX", false);
    }

    public function test_logged_in_customers_see_their_recent_orders(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();

        $this->actingAs($user)->get(route('orders.track'))
            ->assertOk()
            ->assertSee('Your recent orders:')
            ->assertSee($order->order_number);
    }

    public function test_tracking_is_rate_limited(): void
    {
        foreach (range(1, 20) as $attempt) {
            $this->get(route('orders.track', ['order' => "GUESS{$attempt}"]))->assertOk();
        }

        $this->get(route('orders.track', ['order' => 'GUESS21']))->assertTooManyRequests();
    }

    public function test_logout_confirmation_modal_is_shown_to_logged_in_users(): void
    {
        $this->get(route('home'))->assertDontSee('id="logoutModal"', false);

        $this->actingAs(User::factory()->create())->get(route('home'))
            ->assertSee('id="logoutModal"', false)
            ->assertSee('Yes, log out')
            ->assertSee('js-logout-form', false);

        $this->actingAs(User::factory()->admin()->create())->get(route('admin.dashboard'))
            ->assertSee('Log out?')
            ->assertSee('of the admin panel');
    }
}
