<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $this->post(route('register'), [
            'name' => 'Nimali Fernando',
            'email' => 'nimali@example.com',
            'phone' => '0771234567',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'terms' => '1',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertSame('customer', User::sole()->role);
    }

    public function test_customer_can_log_in_and_out(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_admin_is_sent_to_the_dashboard_after_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_cart_survives_login(): void
    {
        $user = User::factory()->create();
        $this->withSession(['cart' => [5 => 1]]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $this->assertSame([5 => 1], session('cart'));
    }
}
