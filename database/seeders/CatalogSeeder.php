<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Main categories: [name, bootstrap icon, sample device, accent colour, show on home, sub-categories].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string, 4: bool, 5: list<string>}>
     */
    private const CATEGORIES = [
        ['Laptops', 'bi-laptop', 'laptop', '#1565c0', true, ['Gaming Laptops', 'Business Laptops', 'Student Laptops', 'MacBooks']],
        ['Desktop PCs', 'bi-pc-display', 'desktop', '#e10600', false, ['Gaming PCs', 'Office PCs', 'All-in-One PCs']],
        ['Monitors', 'bi-display', 'monitor', '#0a3d91', false, []],
        ['Printers & Scanners', 'bi-printer', 'printer', '#e10600', false, ['Ink Tank Printers', 'Laser Printers', 'Ink & Toner']],
        ['Computer Accessories', 'bi-keyboard', 'keyboard', '#e10600', false, ['Keyboards', 'Mice', 'Webcams']],
        ['CCTV & Security', 'bi-camera-video', 'cctv_bullet', '#0a3d91', true, ['CCTV Kits', 'IP Cameras', 'WiFi Cameras']],
        ['Power Banks & Chargers', 'bi-battery-charging', 'powerbank', '#e10600', false, ['Power Banks', 'Chargers & Cables']],
        ['Audio & Soundcore', 'bi-speaker', 'speaker', '#1565c0', true, ['Bluetooth Speakers', 'Headphones', 'Earbuds']],
        ['Networking', 'bi-router', 'router', '#0a3d91', false, []],
        ['Storage', 'bi-device-ssd', 'ssd', '#e10600', false, ['SSDs', 'External Drives', 'Flash Drives']],
    ];

    /**
     * Brand name => logo colour.
     *
     * @var array<string, string>
     */
    private const BRANDS = [
        'HP' => '#0096d6', 'Dell' => '#007db8', 'Lenovo' => '#e2231a', 'ASUS' => '#00539b', 'Acer' => '#83b81a',
        'Apple' => '#111111', 'MSI' => '#d7000f', 'Samsung' => '#1428a0', 'LG' => '#a50034', 'Canon' => '#cc0000',
        'Epson' => '#003399', 'Brother' => '#0d2c6c', 'Logitech' => '#00b8fc', 'Hikvision' => '#e60012',
        'Dahua' => '#e2001a', 'Xiaomi' => '#ff6900', 'Imou' => '#1a73e8', 'Anker' => '#00a7e1', 'Baseus' => '#1c1c1c',
        'Soundcore' => '#00a0e9', 'JBL' => '#ff3300', 'Sony' => '#000000', 'TP-Link' => '#4acbd6',
        'Kingston' => '#c8102e', 'WD' => '#0067b1', 'SanDisk' => '#e2231a',
    ];

    /**
     * Products: [name, sub-category, brand, price, sale price, device, stock, featured, warranty, specifications].
     *
     * @var list<array{0: string, 1: string, 2: string|null, 3: int, 4: int|null, 5: string, 6: int, 7: bool, 8: string, 9: array<string, string>}>
     */
    private const PRODUCTS = [
        // Laptops
        ['ASUS TUF Gaming F15 Core i7 13th Gen RTX 4060', 'Gaming Laptops', 'ASUS', 489000, 459000, 'laptop', 8, true, '2 Years', ['Processor' => 'Intel Core i7-13620H', 'Graphics' => 'NVIDIA RTX 4060 8GB', 'RAM' => '16GB DDR5', 'Storage' => '512GB NVMe SSD', 'Display' => '15.6" FHD 144Hz']],
        ['Lenovo LOQ 15 Ryzen 7 RTX 4050 Gaming Laptop', 'Gaming Laptops', 'Lenovo', 399000, 374900, 'laptop', 12, true, '2 Years', ['Processor' => 'AMD Ryzen 7 7435HS', 'Graphics' => 'NVIDIA RTX 4050 6GB', 'RAM' => '16GB DDR5', 'Storage' => '512GB NVMe SSD', 'Display' => '15.6" FHD 144Hz']],
        ['MSI Katana 15 Core i7 RTX 4070 Gaming Laptop', 'Gaming Laptops', 'MSI', 545000, null, 'laptop', 4, false, '2 Years', ['Processor' => 'Intel Core i7-13620H', 'Graphics' => 'NVIDIA RTX 4070 8GB', 'RAM' => '16GB DDR5', 'Storage' => '1TB NVMe SSD', 'Display' => '15.6" FHD 144Hz']],
        ['Lenovo ThinkPad E14 Gen 5 Core i7', 'Business Laptops', 'Lenovo', 345000, null, 'laptop', 6, false, '3 Years', ['Processor' => 'Intel Core i7-1355U', 'RAM' => '16GB DDR4', 'Storage' => '512GB NVMe SSD', 'Display' => '14" WUXGA IPS', 'OS' => 'Windows 11 Pro']],
        ['HP ProBook 450 G10 Core i5 13th Gen', 'Business Laptops', 'HP', 298000, 285000, 'laptop', 10, false, '3 Years', ['Processor' => 'Intel Core i5-1335U', 'RAM' => '8GB DDR4', 'Storage' => '512GB NVMe SSD', 'Display' => '15.6" FHD', 'OS' => 'Windows 11']],
        ['HP 15 Core i5 13th Gen 8GB 512GB Laptop', 'Student Laptops', 'HP', 219000, 204500, 'laptop', 15, true, '2 Years', ['Processor' => 'Intel Core i5-1335U', 'RAM' => '8GB DDR4', 'Storage' => '512GB NVMe SSD', 'Display' => '15.6" FHD', 'OS' => 'Windows 11 Home']],
        ['Dell Inspiron 15 3530 Core i5 Laptop', 'Student Laptops', 'Dell', 235000, null, 'laptop', 9, false, '2 Years', ['Processor' => 'Intel Core i5-1334U', 'RAM' => '8GB DDR4', 'Storage' => '512GB SSD', 'Display' => '15.6" FHD 120Hz']],
        ['Acer Aspire 3 Ryzen 5 7520U Laptop', 'Student Laptops', 'Acer', 164900, 154900, 'laptop', 20, false, '1 Year', ['Processor' => 'AMD Ryzen 5 7520U', 'RAM' => '8GB LPDDR5', 'Storage' => '512GB SSD', 'Display' => '15.6" FHD']],
        ['Apple MacBook Air 13" M3 8GB 256GB', 'MacBooks', 'Apple', 389000, 369000, 'laptop', 5, true, '1 Year', ['Chip' => 'Apple M3 8-core CPU', 'RAM' => '8GB Unified', 'Storage' => '256GB SSD', 'Display' => '13.6" Liquid Retina', 'Battery' => 'Up to 18 hours']],

        // Desktop PCs
        ['Odin Gaming PC Ryzen 5 7600 RTX 4060', 'Gaming PCs', null, 385000, 365000, 'desktop', 3, true, '3 Years', ['Processor' => 'AMD Ryzen 5 7600', 'Graphics' => 'RTX 4060 8GB', 'RAM' => '16GB DDR5 6000MHz', 'Storage' => '1TB NVMe SSD', 'PSU' => '650W 80+ Bronze']],
        ['Odin Pro Gaming PC Core i7 14700F RTX 4070 Super', 'Gaming PCs', null, 685000, null, 'desktop', 2, false, '3 Years', ['Processor' => 'Intel Core i7-14700F', 'Graphics' => 'RTX 4070 Super 12GB', 'RAM' => '32GB DDR5', 'Storage' => '2TB NVMe SSD', 'PSU' => '750W 80+ Gold']],
        ['HP Pro Tower 290 G9 Core i5 Desktop', 'Office PCs', 'HP', 189000, null, 'desktop', 7, false, '3 Years', ['Processor' => 'Intel Core i5-12400', 'RAM' => '8GB DDR4', 'Storage' => '512GB SSD', 'OS' => 'Windows 11 Pro']],
        ['Dell OptiPlex 7010 SFF Core i7 Desktop', 'Office PCs', 'Dell', 265000, 249000, 'desktop', 5, false, '3 Years', ['Processor' => 'Intel Core i7-13700', 'RAM' => '16GB DDR4', 'Storage' => '512GB SSD', 'Form Factor' => 'Small Form Factor']],
        ['Lenovo IdeaCentre AIO 3 24" Core i5', 'All-in-One PCs', 'Lenovo', 245000, 232000, 'aio', 4, false, '2 Years', ['Processor' => 'Intel Core i5-13420H', 'RAM' => '8GB DDR4', 'Storage' => '512GB SSD', 'Display' => '23.8" FHD IPS']],

        // Monitors
        ['Samsung 24" Essential S3 FHD 100Hz Monitor', 'Monitors', 'Samsung', 38500, 34900, 'monitor', 25, true, '3 Years', ['Size' => '24 inch', 'Resolution' => '1920 x 1080', 'Refresh Rate' => '100Hz', 'Panel' => 'IPS']],
        ['Dell 27" S2721HN IPS Monitor', 'Monitors', 'Dell', 62000, null, 'monitor', 10, false, '3 Years', ['Size' => '27 inch', 'Resolution' => '1920 x 1080', 'Refresh Rate' => '75Hz', 'Ports' => '2x HDMI']],
        ['ASUS TUF Gaming VG249Q1A 24" 165Hz Monitor', 'Monitors', 'ASUS', 58900, 54500, 'monitor', 12, false, '3 Years', ['Size' => '23.8 inch', 'Refresh Rate' => '165Hz', 'Response Time' => '1ms MPRT', 'Panel' => 'IPS']],
        ['LG UltraGear 27" QHD 180Hz Gaming Monitor', 'Monitors', 'LG', 98500, null, 'monitor', 6, false, '3 Years', ['Size' => '27 inch', 'Resolution' => '2560 x 1440', 'Refresh Rate' => '180Hz', 'HDR' => 'HDR10']],
        ['MSI MAG 32" Curved 165Hz Gaming Monitor', 'Monitors', 'MSI', 115000, 109000, 'monitor', 3, false, '3 Years', ['Size' => '31.5 inch', 'Curvature' => '1500R', 'Refresh Rate' => '165Hz', 'Resolution' => '2560 x 1440']],

        // Printers
        ['Epson EcoTank L3250 WiFi All-in-One Printer', 'Ink Tank Printers', 'Epson', 64500, 59900, 'printer', 14, true, '2 Years', ['Functions' => 'Print, Scan, Copy', 'Connectivity' => 'WiFi, USB', 'Print Speed' => '33 ppm (black)']],
        ['Canon PIXMA G3020 Refillable Ink Tank Printer', 'Ink Tank Printers', 'Canon', 58000, null, 'printer', 9, false, '1 Year', ['Functions' => 'Print, Scan, Copy', 'Connectivity' => 'WiFi, USB', 'Yield' => '6,000 pages black']],
        ['HP Smart Tank 580 All-in-One Printer', 'Ink Tank Printers', 'HP', 61500, 57900, 'printer', 7, false, '1 Year', ['Functions' => 'Print, Scan, Copy', 'Connectivity' => 'WiFi, Bluetooth', 'Yield' => '12,000 pages black']],
        ['Brother HL-L2350DW Mono Laser Printer', 'Laser Printers', 'Brother', 72000, null, 'laser', 5, false, '1 Year', ['Type' => 'Mono Laser', 'Print Speed' => '30 ppm', 'Duplex' => 'Automatic', 'Connectivity' => 'WiFi, USB']],
        ['HP LaserJet Pro MFP 4103fdw', 'Laser Printers', 'HP', 185000, 175000, 'laser', 2, false, '1 Year', ['Functions' => 'Print, Scan, Copy, Fax', 'Print Speed' => '40 ppm', 'Duplex' => 'Automatic']],
        ['Epson 003 Genuine Ink Bottle Set (4 Colours)', 'Ink & Toner', 'Epson', 8400, 7600, 'ink', 60, false, 'N/A', ['Colours' => 'Black, Cyan, Magenta, Yellow', 'Compatible' => 'L1110, L3110, L3150, L3250']],

        // Accessories
        ['Logitech MK270 Wireless Keyboard & Mouse Combo', 'Keyboards', 'Logitech', 8900, 7950, 'keyboard', 40, false, '1 Year', ['Connection' => '2.4GHz USB receiver', 'Battery' => 'Up to 36 months (keyboard)', 'Layout' => 'Full size']],
        ['Logitech G413 SE Mechanical Gaming Keyboard', 'Keyboards', 'Logitech', 24500, null, 'keyboard', 11, false, '2 Years', ['Switches' => 'Tactile mechanical', 'Backlight' => 'White LED', 'Frame' => 'Aluminium']],
        ['Logitech G102 Lightsync RGB Gaming Mouse', 'Mice', 'Logitech', 6900, 5990, 'mouse', 35, true, '2 Years', ['Sensor' => '8,000 DPI', 'Buttons' => '6 programmable', 'Lighting' => 'RGB Lightsync']],
        ['Logitech MX Master 3S Wireless Mouse', 'Mice', 'Logitech', 36500, null, 'mouse', 8, false, '1 Year', ['Sensor' => '8,000 DPI', 'Connection' => 'Bluetooth, Logi Bolt', 'Battery' => 'Up to 70 days']],
        ['Logitech C270 HD Webcam', 'Webcams', 'Logitech', 9800, 8900, 'webcam', 22, false, '1 Year', ['Resolution' => '720p / 30fps', 'Microphone' => 'Built-in noise reducing', 'Connection' => 'USB']],

        // CCTV
        ['Hikvision 4 Channel 2MP CCTV Kit with 1TB HDD', 'CCTV Kits', 'Hikvision', 78500, 72900, 'cctv_kit', 10, true, '2 Years', ['Cameras' => '4 x 2MP (2 Bullet, 2 Dome)', 'DVR' => '4 Channel Turbo HD', 'Storage' => '1TB Surveillance HDD', 'Remote View' => 'Hik-Connect app']],
        ['Dahua 8 Channel 5MP CCTV Kit with 2TB HDD', 'CCTV Kits', 'Dahua', 145000, null, 'cctv_kit', 4, false, '2 Years', ['Cameras' => '8 x 5MP', 'DVR' => '8 Channel XVR', 'Storage' => '2TB HDD', 'Night Vision' => 'Up to 30m']],
        ['Hikvision 4MP ColorVu IP Bullet Camera', 'IP Cameras', 'Hikvision', 22500, null, 'cctv_bullet', 18, false, '2 Years', ['Resolution' => '4MP', 'Night Vision' => '24/7 colour imaging', 'Rating' => 'IP67 weatherproof', 'Power' => 'PoE']],
        ['Dahua 4MP IP Dome Camera', 'IP Cameras', 'Dahua', 18900, 17500, 'cctv_dome', 16, false, '2 Years', ['Resolution' => '4MP', 'Lens' => '2.8mm wide angle', 'IR Range' => '30m', 'Power' => 'PoE']],
        ['Xiaomi Mi 360° Home Security Camera 2K', 'WiFi Cameras', 'Xiaomi', 14500, 12900, 'wificam', 30, true, '1 Year', ['Resolution' => '2K (2304 x 1296)', 'Rotation' => '360° pan', 'Storage' => 'MicroSD up to 256GB', 'Audio' => 'Two-way talk']],
        ['Imou Ranger 2 WiFi Camera', 'WiFi Cameras', 'Imou', 11900, null, 'wificam', 21, false, '1 Year', ['Resolution' => '1080p', 'Tracking' => 'Smart human tracking', 'Night Vision' => '10m']],

        // Power
        ['Anker PowerCore 20000mAh PD 20W Power Bank', 'Power Banks', 'Anker', 16500, 14900, 'powerbank', 26, true, '18 Months', ['Capacity' => '20,000mAh', 'Output' => '20W USB-C PD', 'Ports' => '1x USB-C, 2x USB-A']],
        ['Baseus Blade 20000mAh 100W Laptop Power Bank', 'Power Banks', 'Baseus', 24900, null, 'powerbank', 9, false, '1 Year', ['Capacity' => '20,000mAh', 'Output' => '100W USB-C PD', 'Display' => 'Digital LED']],
        ['Xiaomi Redmi 10000mAh Fast Charge Power Bank', 'Power Banks', 'Xiaomi', 5990, 5490, 'powerbank', 50, false, '6 Months', ['Capacity' => '10,000mAh', 'Output' => '18W fast charge', 'Ports' => '2x USB-A, 1x USB-C']],
        ['Anker 735 Nano II 65W GaN Charger', 'Chargers & Cables', 'Anker', 14900, null, 'charger', 17, false, '18 Months', ['Output' => '65W total', 'Ports' => '2x USB-C, 1x USB-A', 'Technology' => 'GaN II']],
        ['Baseus 100W USB-C to USB-C Cable 2m', 'Chargers & Cables', 'Baseus', 3490, 2990, 'cable', 80, false, '6 Months', ['Length' => '2 metres', 'Power' => '100W PD', 'Material' => 'Braided nylon']],

        // Audio
        ['Anker Soundcore Motion+ Bluetooth Speaker', 'Bluetooth Speakers', 'Soundcore', 39900, 35900, 'speaker', 12, true, '18 Months', ['Output' => '30W Hi-Res audio', 'Battery' => '12 hours', 'Water Resistance' => 'IPX7', 'Bluetooth' => '5.0']],
        ['Soundcore Boom 2 Outdoor Speaker', 'Bluetooth Speakers', 'Soundcore', 42500, null, 'speaker', 7, false, '18 Months', ['Output' => '80W BassUp 2.0', 'Battery' => '24 hours', 'Water Resistance' => 'IPX7', 'Lighting' => 'RGB lights']],
        ['JBL Flip 6 Portable Waterproof Speaker', 'Bluetooth Speakers', 'JBL', 38900, 34900, 'speaker', 14, false, '1 Year', ['Battery' => '12 hours', 'Water Resistance' => 'IP67', 'PartyBoost' => 'Yes']],
        ['JBL Go 4 Ultra Portable Speaker', 'Bluetooth Speakers', 'JBL', 12900, null, 'speaker_small', 30, false, '1 Year', ['Battery' => '7 hours', 'Water Resistance' => 'IP67', 'Bluetooth' => '5.3 LE audio']],
        ['Sony WH-1000XM5 Noise Cancelling Headphones', 'Headphones', 'Sony', 115000, 104900, 'headphones', 5, true, '1 Year', ['Noise Cancelling' => 'Industry leading ANC', 'Battery' => '30 hours', 'Charging' => '3 min = 3 hours']],
        ['Soundcore Life Q30 Hybrid ANC Headphones', 'Headphones', 'Soundcore', 24900, 21900, 'headphones', 19, false, '18 Months', ['Noise Cancelling' => 'Hybrid ANC', 'Battery' => '40 hours', 'Audio' => 'Hi-Res certified']],
        ['Soundcore Liberty 4 NC True Wireless Earbuds', 'Earbuds', 'Soundcore', 29900, null, 'earbuds', 15, false, '18 Months', ['Noise Cancelling' => 'Adaptive ANC 2.0', 'Battery' => '50 hours with case', 'Charging' => 'Wireless']],
        ['JBL Tune Beam True Wireless Earbuds', 'Earbuds', 'JBL', 26500, 23900, 'earbuds', 0, false, '1 Year', ['Noise Cancelling' => 'Active NC', 'Battery' => '48 hours with case', 'Water Resistance' => 'IP54']],

        // Networking
        ['TP-Link Archer C6 AC1200 Dual Band WiFi Router', 'Networking', 'TP-Link', 12500, 10900, 'router', 24, false, '3 Years', ['Speed' => '1200 Mbps dual band', 'Ports' => '4x Gigabit LAN', 'Antennas' => '4 external']],
        ['TP-Link Deco X20 AX1800 Mesh WiFi 6 (2-Pack)', 'Networking', 'TP-Link', 42000, null, 'mesh', 8, true, '3 Years', ['Standard' => 'WiFi 6 AX1800', 'Coverage' => 'Up to 4,000 sq ft', 'Units' => '2']],
        ['TP-Link TL-SG108 8-Port Gigabit Switch', 'Networking', 'TP-Link', 7900, null, 'switch', 33, false, 'Lifetime', ['Ports' => '8x Gigabit RJ45', 'Housing' => 'Metal', 'Setup' => 'Plug and play']],
        ['ASUS RT-AX58U AX3000 WiFi 6 Router', 'Networking', 'ASUS', 38500, 35500, 'router', 6, false, '3 Years', ['Standard' => 'WiFi 6 AX3000', 'Security' => 'AiProtection Pro', 'Mesh' => 'AiMesh support']],

        // Storage
        ['Samsung 980 1TB NVMe M.2 SSD', 'SSDs', 'Samsung', 26500, 23900, 'ssd', 20, true, '5 Years', ['Capacity' => '1TB', 'Interface' => 'PCIe 3.0 x4 NVMe', 'Read Speed' => '3,500 MB/s']],
        ['Kingston A400 480GB SATA SSD', 'SSDs', 'Kingston', 11500, null, 'ssd_sata', 28, false, '3 Years', ['Capacity' => '480GB', 'Interface' => 'SATA III', 'Read Speed' => '500 MB/s']],
        ['WD My Passport 2TB Portable Hard Drive', 'External Drives', 'WD', 28900, 26900, 'hdd', 13, false, '3 Years', ['Capacity' => '2TB', 'Interface' => 'USB 3.2', 'Security' => '256-bit AES hardware encryption']],
        ['SanDisk Ultra Flair 64GB USB 3.0 Flash Drive', 'Flash Drives', 'SanDisk', 2450, 1990, 'flash', 90, false, '5 Years', ['Capacity' => '64GB', 'Interface' => 'USB 3.0', 'Speed' => 'Up to 150 MB/s']],
    ];

    private const ACCENTS = ['#e10600', '#1565c0', '#0a3d91'];

    public function __construct(private SampleImageFactory $images) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['products', 'categories', 'brands', 'banners'] as $directory) {
            Storage::disk('public')->deleteDirectory($directory);
        }

        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $this->seedProducts($categories, $brands);
        $this->seedBanners();
    }

    /**
     * @return array<string, array{category: Category, accent: string}>
     */
    private function seedCategories(): array
    {
        $lookup = [];

        foreach (self::CATEGORIES as $index => [$name, $icon, $device, $accent, $showOnHome, $children]) {
            $slug = Str::slug($name);

            $parent = Category::create([
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon,
                'image' => $this->images->category("categories/{$slug}.svg", $device, $accent),
                'description' => "Shop the latest {$name} in Sri Lanka at the best prices with genuine warranty and islandwide delivery.",
                'show_on_home' => $showOnHome,
                'sort_order' => $index + 1,
            ]);

            $lookup[$name] = ['category' => $parent, 'accent' => $accent];

            foreach ($children as $childIndex => $childName) {
                $lookup[$childName] = [
                    'category' => $parent->children()->create([
                        'name' => $childName,
                        'slug' => Str::slug($childName),
                        'icon' => $icon,
                        'sort_order' => $childIndex + 1,
                    ]),
                    'accent' => $accent,
                ];
            }
        }

        return $lookup;
    }

    /**
     * @return array<string, Brand>
     */
    private function seedBrands(): array
    {
        $brands = [];

        foreach (self::BRANDS as $name => $color) {
            $slug = Str::slug($name);

            $brands[$name] = Brand::create([
                'name' => $name,
                'slug' => $slug,
                'logo' => $this->images->brandLogo("brands/{$slug}.svg", $name, $color),
            ]);
        }

        return $brands;
    }

    /**
     * @param  array<string, array{category: Category, accent: string}>  $categories
     * @param  array<string, Brand>  $brands
     */
    private function seedProducts(array $categories, array $brands): void
    {
        foreach (self::PRODUCTS as $index => [$name, $categoryName, $brandName, $price, $salePrice, $device, $stock, $featured, $warranty, $specs]) {
            $slug = Str::slug($name);
            $category = $categories[$categoryName];
            $brandLabel = $brandName ?? 'Odin';

            $product = Product::create([
                'category_id' => $category['category']->id,
                'brand_id' => $brandName ? $brands[$brandName]->id : null,
                'name' => $name,
                'slug' => $slug,
                'sku' => 'ODN-'.str_pad((string) ($index + 1001), 5, '0', STR_PAD_LEFT),
                'short_description' => collect($specs)->take(3)->map(fn ($value, $key) => "{$key}: {$value}")->implode(' | '),
                'description' => $this->description($name, $brandLabel, $categoryName, $warranty),
                'specifications' => $specs + ['Warranty' => $warranty],
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => $stock,
                'warranty' => $warranty,
                'is_featured' => $featured,
            ]);

            $accents = [$category['accent'], ...array_values(array_diff(self::ACCENTS, [$category['accent']]))];

            foreach (range(0, 2) as $variant) {
                $product->images()->create([
                    'path' => $this->images->product("products/{$slug}-{$variant}.svg", $device, $accents[$variant], $brandLabel, $variant),
                    'is_primary' => $variant === 0,
                    'sort_order' => $variant,
                ]);
            }
        }
    }

    private function seedBanners(): void
    {
        $banners = [
            ['slider', 'Gaming Laptop Mega Sale', 'Up to 15% OFF on ASUS, MSI & Lenovo', 'laptop', '#e10600', 'dark', '/category/laptops'],
            ['slider', 'Secure Your Home', 'CCTV kits with free installation in Colombo', 'cctv_kit', '#1565c0', 'blue', '/category/cctv-security'],
            ['slider', 'Soundcore Audio Week', 'Speakers, headphones & earbuds from Rs. 12,900', 'speaker', '#e10600', 'dark', '/category/audio-soundcore'],
            ['side', 'Power Banks', 'Starting from Rs. 5,490', 'powerbank', '#ffffff', 'red', '/category/power-banks-chargers'],
            ['side', 'Ink Tank Printers', 'EcoTank & Smart Tank deals', 'printer', '#e10600', 'blue', '/category/printers-scanners'],
            ['wide', 'Hot Deals This Week', 'Save up to 20% on laptops, monitors & accessories', 'monitor', '#e10600', 'dark', '/deals'],
        ];

        foreach ($banners as $index => [$position, $title, $subtitle, $device, $accent, $theme, $link]) {
            Banner::create([
                'title' => $title,
                'subtitle' => $subtitle,
                'image' => $this->images->banner("banners/banner-{$index}.svg", $position, $title, $subtitle, $device, $accent, $theme),
                'link' => $link,
                'position' => $position,
                'sort_order' => $index,
            ]);
        }
    }

    private function description(string $name, string $brand, string $category, string $warranty): string
    {
        return <<<TEXT
The {$name} brings dependable {$brand} quality to your home or office. It is a great choice in our {$category} range, backed by an official {$warranty} warranty in Sri Lanka.

Every product sold on Odin.lk is 100% genuine and brand new. Order online and enjoy fast islandwide delivery, cash on delivery and friendly after-sales support from our Colombo showroom.

What's in the box: {$name}, user manual and warranty card.
TEXT;
    }
}
