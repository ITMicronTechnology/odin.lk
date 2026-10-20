<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => 'SKU-'.fake()->unique()->numerify('#####'),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'specifications' => ['RAM' => '16GB'],
            'price' => 10000,
            'sale_price' => null,
            'stock' => 10,
            'warranty' => '1 Year',
            'is_featured' => false,
            'is_active' => true,
        ];
    }

    public function onSale(float $salePrice): static
    {
        return $this->state(fn (array $attributes) => ['sale_price' => $salePrice]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => ['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
