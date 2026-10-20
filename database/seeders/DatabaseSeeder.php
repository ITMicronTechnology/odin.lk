<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Odin Admin',
            'email' => 'admin@odin.lk',
            'phone' => '0771234567',
        ]);

        User::factory()->create([
            'name' => 'Kasun Perera',
            'email' => 'customer@odin.lk',
            'phone' => '0712345678',
            'address' => 'No. 42, Temple Road',
            'city' => 'Nugegoda',
            'district' => 'Colombo',
            'postal_code' => '10250',
        ]);

        $this->call([
            CatalogSeeder::class,
            MobilePhoneSeeder::class,
            BrandLogoSeeder::class,
            ServiceSeeder::class,
            DemoOrderSeeder::class,
            ProductReviewSeeder::class,
        ]);
    }
}
