<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ODN'.fake()->unique()->numerify('##########'),
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'payment_status' => 'unpaid',
            'subtotal' => 10000,
            'shipping_fee' => 450,
            'discount' => 0,
            'total' => 10450,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => '0771234567',
            'address' => fake()->streetAddress(),
            'city' => 'Colombo',
            'district' => 'Colombo',
        ];
    }
}
