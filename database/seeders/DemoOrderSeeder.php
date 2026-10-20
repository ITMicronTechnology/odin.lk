<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoOrderSeeder extends Seeder
{
    /**
     * Sample customers: [name, email, phone, city, district].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string, 4: string}>
     */
    private const CUSTOMERS = [
        ['Nimali Fernando', 'nimali@example.com', '0773456789', 'Kandy', 'Kandy'],
        ['Tharindu Silva', 'tharindu@example.com', '0764567890', 'Galle', 'Galle'],
        ['Ayesha Jayawardena', 'ayesha@example.com', '0755678901', 'Negombo', 'Gampaha'],
        ['Ruwan Bandara', 'ruwan@example.com', '0706789012', 'Kurunegala', 'Kurunegala'],
    ];

    /**
     * Seed a few weeks of demo orders so the admin dashboard has data to show.
     */
    public function run(): void
    {
        $customers = User::where('email', 'customer@odin.lk')->get();

        foreach (self::CUSTOMERS as [$name, $email, $phone, $city, $district]) {
            $customers->push(User::factory()->create(compact('name', 'email', 'phone', 'city', 'district') + [
                'address' => fake()->buildingNumber().', '.fake()->streetName(),
            ]));
        }

        $products = Product::where('stock', '>', 0)->get();
        $statuses = [
            OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Shipped,
            OrderStatus::Processing, OrderStatus::Confirmed, OrderStatus::Pending, OrderStatus::Pending,
            OrderStatus::Cancelled,
        ];

        foreach (range(1, 22) as $index) {
            $customer = $customers->random();
            $status = $statuses[array_rand($statuses)];
            $placedAt = now()->subDays(random_int(0, 13))->subMinutes(random_int(0, 600));
            $lines = $products->random(random_int(1, 3));

            $subtotal = $lines->sum(fn (Product $product) => $product->final_price);
            $shipping = $subtotal >= config('shop.free_shipping_threshold') ? 0 : config('shop.shipping_fee');

            $order = Order::create([
                'order_number' => 'ODN'.$placedAt->format('ymd').str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                'user_id' => $customer->id,
                'status' => $status,
                'payment_method' => $index % 3 === 0 ? PaymentMethod::BankTransfer : PaymentMethod::CashOnDelivery,
                'payment_status' => $status === OrderStatus::Delivered ? 'paid' : 'unpaid',
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total' => $subtotal + $shipping,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'address' => $customer->address,
                'city' => $customer->city,
                'district' => $customer->district,
            ]);
            $order->forceFill(['created_at' => $placedAt, 'updated_at' => $placedAt])->save();

            foreach ($lines as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->final_price,
                    'quantity' => 1,
                    'line_total' => $product->final_price,
                ]);
            }

            $timeline = $status === OrderStatus::Cancelled
                ? [OrderStatus::Pending, OrderStatus::Cancelled]
                : array_slice(OrderStatus::timeline(), 0, array_search($status, OrderStatus::timeline(), true) + 1);

            foreach ($timeline as $step => $historyStatus) {
                $order->histories()->create(['status' => $historyStatus->value])
                    ->forceFill(['created_at' => $placedAt->copy()->addHours($step * 20)])
                    ->save();
            }
        }
    }
}
