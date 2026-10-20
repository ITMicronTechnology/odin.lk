<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true)).' Service';

        return [
            'service_category_id' => fn () => ServiceCategory::create([
                'name' => $category = Str::title(fake()->unique()->words(2, true)),
                'slug' => Str::slug($category),
            ])->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'bi-globe2',
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'features' => ['Feature one', 'Feature two'],
            'starting_price' => 50000,
            'price_note' => 'one-time',
            'delivery_time' => '2 weeks',
            'is_active' => true,
        ];
    }
}
