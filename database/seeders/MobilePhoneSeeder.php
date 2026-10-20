<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Adds the Mobile Phones department (categories, brands, sample products and images).
 *
 * Safe to run on a live database: it only creates what is missing and never
 * changes products, orders or images that already exist.
 */
class MobilePhoneSeeder extends Seeder
{
    private const ACCENT = '#0a3d91';

    /**
     * Sub-categories created under Mobile Phones, in menu order.
     *
     * @var list<string>
     */
    private const SUB_CATEGORIES = ['Smartphones', 'Tablets', 'Smartwatches', 'Feature Phones', 'Mobile Accessories'];

    /**
     * New brand name => logo colour (existing brands such as Samsung and Apple are reused).
     *
     * @var array<string, string>
     */
    private const BRANDS = [
        'OPPO' => '#046a38', 'vivo' => '#415fff', 'realme' => '#d9a400', 'Google' => '#4285f4',
        'Honor' => '#111111', 'Tecno' => '#0055ff', 'Nokia' => '#124191',
    ];

    /**
     * Products: [name, sub-category, brand, price, sale price, device, back colour, stock, featured, warranty, specifications].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: int, 4: int|null, 5: string, 6: string, 7: int, 8: bool, 9: string, 10: array<string, string>}>
     */
    private const PRODUCTS = [
        // Smartphones
        ['Samsung Galaxy S25 Ultra 12GB 256GB', 'Smartphones', 'Samsung', 419999, 399999, 'phone_s', '#4b5563', 6, true, '1 Year', ['Display' => '6.9" QHD+ Dynamic AMOLED 120Hz', 'Processor' => 'Snapdragon 8 Elite', 'Camera' => '200MP + 50MP + 50MP + 10MP', 'Battery' => '5000mAh, 45W', 'Storage' => '12GB RAM / 256GB']],
        ['Samsung Galaxy A56 5G 8GB 256GB', 'Smartphones', 'Samsung', 154999, 144999, 'phone_s', '#9ec5e8', 18, true, '1 Year', ['Display' => '6.7" FHD+ Super AMOLED 120Hz', 'Processor' => 'Exynos 1580', 'Camera' => '50MP + 12MP + 5MP', 'Battery' => '5000mAh, 45W', 'Storage' => '8GB RAM / 256GB']],
        ['Samsung Galaxy A16 4GB 128GB', 'Smartphones', 'Samsung', 54999, null, 'phone_s', '#1f2937', 30, false, '1 Year', ['Display' => '6.7" FHD+ Super AMOLED', 'Camera' => '50MP + 5MP + 2MP', 'Battery' => '5000mAh, 25W', 'Storage' => '4GB RAM / 128GB']],
        ['Apple iPhone 16 Pro 128GB', 'Smartphones', 'Apple', 389000, 374900, 'phone', '#c9b8a3', 5, true, '1 Year', ['Display' => '6.3" Super Retina XDR ProMotion', 'Chip' => 'A18 Pro', 'Camera' => '48MP Fusion + 48MP Ultra Wide + 12MP 5x', 'Storage' => '128GB']],
        ['Apple iPhone 16 128GB', 'Smartphones', 'Apple', 289000, null, 'phone', '#7fb7d9', 8, false, '1 Year', ['Display' => '6.1" Super Retina XDR', 'Chip' => 'A18', 'Camera' => '48MP Fusion + 12MP Ultra Wide', 'Storage' => '128GB']],
        ['Xiaomi Redmi Note 14 Pro 8GB 256GB', 'Smartphones', 'Xiaomi', 94999, 89999, 'phone', '#6d5bd0', 25, true, '1 Year', ['Display' => '6.67" 1.5K AMOLED 120Hz', 'Camera' => '200MP + 8MP + 2MP', 'Battery' => '5500mAh, 45W', 'Storage' => '8GB RAM / 256GB']],
        ['Xiaomi 14T 12GB 512GB', 'Smartphones', 'Xiaomi', 179999, null, 'phone', '#374151', 7, false, '1 Year', ['Display' => '6.67" 1.5K AMOLED 144Hz', 'Camera' => '50MP Leica triple camera', 'Battery' => '5000mAh, 67W', 'Storage' => '12GB RAM / 512GB']],
        ['OPPO Reno 13 5G 12GB 256GB', 'Smartphones', 'OPPO', 164999, 154999, 'phone', '#a7c7e7', 12, false, '1 Year', ['Display' => '6.59" AMOLED 120Hz', 'Camera' => '50MP + 8MP + 2MP', 'Battery' => '5600mAh, 80W SUPERVOOC', 'Storage' => '12GB RAM / 256GB']],
        ['vivo V40 5G 12GB 256GB', 'Smartphones', 'vivo', 149999, null, 'phone_s', '#8a6fb5', 10, false, '1 Year', ['Display' => '6.78" 3D curved AMOLED 120Hz', 'Camera' => '50MP ZEISS dual camera', 'Battery' => '5500mAh, 80W', 'Storage' => '12GB RAM / 256GB']],
        ['realme 13 Pro+ 5G 12GB 512GB', 'Smartphones', 'realme', 129999, 119999, 'phone', '#c8b27a', 9, false, '1 Year', ['Display' => '6.7" curved OLED 120Hz', 'Camera' => '50MP Sony periscope', 'Battery' => '5200mAh, 80W', 'Storage' => '12GB RAM / 512GB']],
        ['Google Pixel 9 12GB 128GB', 'Smartphones', 'Google', 239000, null, 'phone_s', '#f2c6c2', 4, false, '1 Year', ['Display' => '6.3" Actua OLED 120Hz', 'Chip' => 'Google Tensor G4', 'Camera' => '50MP + 48MP', 'Storage' => '12GB RAM / 128GB']],
        ['Honor X9c 5G 12GB 256GB', 'Smartphones', 'Honor', 109999, 104999, 'phone', '#b4884c', 14, false, '1 Year', ['Display' => '6.78" curved AMOLED 120Hz', 'Camera' => '108MP + 5MP', 'Battery' => '6600mAh, 66W', 'Storage' => '12GB RAM / 256GB']],
        ['Tecno Spark 30 8GB 256GB', 'Smartphones', 'Tecno', 44999, 41999, 'phone', '#2563eb', 35, false, '1 Year', ['Display' => '6.78" FHD+ 90Hz', 'Camera' => '64MP Sony', 'Battery' => '5000mAh, 18W', 'Storage' => '8GB RAM / 256GB']],

        // Feature phones
        ['Nokia 105 (2024) Dual SIM', 'Feature Phones', 'Nokia', 6990, null, 'feature_phone', '#1d4ed8', 60, false, '1 Year', ['Display' => '1.8" QQVGA', 'Battery' => '1000mAh, up to 12 days standby', 'SIM' => 'Dual SIM', 'Extras' => 'FM radio, torch']],
        ['Nokia 110 4G Dual SIM', 'Feature Phones', 'Nokia', 11990, 10990, 'feature_phone', '#0f766e', 40, false, '1 Year', ['Display' => '2.0" QVGA', 'Network' => '4G VoLTE', 'Camera' => 'Rear camera', 'Battery' => '1450mAh']],

        // Tablets
        ['Samsung Galaxy Tab A9+ 11" WiFi 8GB 128GB', 'Tablets', 'Samsung', 74999, 69999, 'tablet', '#4b5563', 12, true, '1 Year', ['Display' => '11" 90Hz', 'Speakers' => 'Quad speakers, Dolby Atmos', 'Battery' => '7040mAh', 'Storage' => '8GB RAM / 128GB']],
        ['Apple iPad 10th Gen 10.9" WiFi 64GB', 'Tablets', 'Apple', 149000, null, 'tablet', '#60a5fa', 6, false, '1 Year', ['Display' => '10.9" Liquid Retina', 'Chip' => 'A14 Bionic', 'Camera' => '12MP landscape front camera', 'Storage' => '64GB']],
        ['Xiaomi Pad 7 8GB 256GB', 'Tablets', 'Xiaomi', 124999, 117999, 'tablet', '#374151', 9, false, '1 Year', ['Display' => '11.2" 3.2K 144Hz', 'Processor' => 'Snapdragon 7+ Gen 3', 'Battery' => '8850mAh, 45W', 'Storage' => '8GB RAM / 256GB']],

        // Smartwatches
        ['Samsung Galaxy Watch 7 44mm', 'Smartwatches', 'Samsung', 94999, 84999, 'smartwatch', '#10b981', 11, false, '1 Year', ['Display' => '1.5" Super AMOLED', 'Health' => 'BioActive sensor, sleep coaching', 'Battery' => 'Up to 40 hours', 'Water Resistance' => '5ATM + IP68']],
        ['Apple Watch SE (2nd Gen) 44mm GPS', 'Smartwatches', 'Apple', 99000, null, 'smartwatch', '#e11d48', 7, false, '1 Year', ['Display' => 'Retina LTPO OLED', 'Safety' => 'Crash & fall detection', 'Water Resistance' => '50m']],
        ['Xiaomi Redmi Watch 5 Active', 'Smartwatches', 'Xiaomi', 11999, 10499, 'smartwatch', '#f59e0b', 45, false, '1 Year', ['Display' => '2" LCD', 'Calls' => 'Bluetooth calling', 'Battery' => 'Up to 18 days', 'Sports Modes' => '140+']],

        // Accessories
        ['Samsung 25W Super Fast Charger USB-C', 'Mobile Accessories', 'Samsung', 6490, 5990, 'charger', '#1f2937', 70, false, '6 Months', ['Output' => '25W PD 3.0', 'Port' => 'USB-C', 'Compatible' => 'Galaxy S, A and Z series']],
        ['Apple 20W USB-C Power Adapter', 'Mobile Accessories', 'Apple', 8900, null, 'charger', '#9ca3af', 50, false, '1 Year', ['Output' => '20W', 'Port' => 'USB-C', 'Compatible' => 'iPhone and iPad']],
        ['Samsung Galaxy Buds FE', 'Mobile Accessories', 'Samsung', 24999, 21999, 'earbuds', '#6b7280', 20, false, '1 Year', ['Noise Cancelling' => 'Active Noise Cancelling', 'Battery' => 'Up to 30 hours with case', 'Fit' => 'Wing tip ergonomic fit']],
    ];

    public function __construct(private SampleImageFactory $images) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = $this->seedCategories();
        $children = $parent->children()->get()->keyBy('name');
        $brands = $this->seedBrands();

        foreach (self::PRODUCTS as $index => [$name, $subCategory, $brandName, $price, $salePrice, $device, $color, $stock, $featured, $warranty, $specs]) {
            $slug = Str::slug($name);

            if (Product::where('slug', $slug)->exists()) {
                continue;
            }

            $product = Product::create([
                'category_id' => $children[$subCategory]->id,
                'brand_id' => $brands[$brandName]->id,
                'name' => $name,
                'slug' => $slug,
                'sku' => 'ODN-M'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'short_description' => collect($specs)->take(3)->map(fn ($value, $key) => "{$key}: {$value}")->implode(' | '),
                'description' => "The {$name} is available now at Odin.lk with an official {$warranty} warranty in Sri Lanka.\n\nEvery phone we sell is 100% genuine and brand new, with islandwide delivery and cash on delivery.\n\nWhat's in the box: {$name}, user guide and warranty card.",
                'specifications' => $specs + ['Warranty' => $warranty],
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => $stock,
                'warranty' => $warranty,
                'is_featured' => $featured,
            ]);

            foreach ([$color, self::ACCENT, '#e10600'] as $variant => $accent) {
                $product->images()->create([
                    'path' => $this->images->product("products/{$slug}-{$variant}.svg", $device, $accent, $brandName, $variant),
                    'is_primary' => $variant === 0,
                    'sort_order' => $variant,
                ]);
            }
        }

        Banner::firstOrCreate(['link' => '/category/mobile-phones', 'position' => 'slider'], [
            'title' => 'Latest Smartphones',
            'subtitle' => 'Samsung, iPhone, Xiaomi & more from Rs. 41,999',
            'image' => $this->images->banner('banners/banner-mobiles.svg', 'slider', 'Latest Smartphones', 'Samsung, iPhone, Xiaomi & more from Rs. 41,999', 'phone_s', '#e10600', 'blue'),
            'sort_order' => 1,
        ]);
    }

    private function seedCategories(): Category
    {
        $parent = Category::firstOrCreate(['slug' => 'mobile-phones'], [
            'name' => 'Mobile Phones',
            'icon' => 'bi-phone',
            'image' => $this->images->category('categories/mobile-phones.svg', 'phone_s', self::ACCENT),
            'description' => 'Shop the latest smartphones, tablets, smartwatches and mobile accessories in Sri Lanka with genuine warranty and islandwide delivery.',
            'show_on_home' => true,
            'sort_order' => 2,
        ]);

        // Place Mobile Phones right after Laptops in the menu the first time it is added.
        if ($parent->wasRecentlyCreated) {
            Category::roots()->whereKeyNot($parent->id)->where('sort_order', '>=', 2)->increment('sort_order');
        }

        foreach (self::SUB_CATEGORIES as $index => $name) {
            $parent->children()->firstOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'icon' => 'bi-phone',
                'sort_order' => $index + 1,
            ]);
        }

        return $parent;
    }

    /**
     * @return array<string, Brand>
     */
    private function seedBrands(): array
    {
        foreach (self::BRANDS as $name => $color) {
            $slug = Str::slug($name);

            Brand::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'logo' => $this->images->brandLogo("brands/{$slug}.svg", $name, $color),
            ]);
        }

        return Brand::whereIn('name', collect(self::PRODUCTS)->pluck(2)->unique())->get()->keyBy('name')->all();
    }
}
