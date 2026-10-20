<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Replaces the generated sample brand logos with the real logos in public/images/brands.
 *
 * Only brands that still use a sample logo (or have none) are changed, so logos uploaded
 * through the admin panel are never overwritten. Safe to run again.
 */
class BrandLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Brand::all() as $brand) {
            $source = public_path("images/brands/{$brand->slug}.webp");
            $usesSampleLogo = $brand->logo === null || $brand->logo === "brands/{$brand->slug}.svg";

            if (! $usesSampleLogo || ! is_file($source)) {
                continue;
            }

            $path = "brands/{$brand->slug}.webp";
            Storage::disk('public')->put($path, file_get_contents($source));

            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }

            $brand->update(['logo' => $path]);
            $this->command?->info("Updated logo: {$brand->name}");
        }
    }
}
