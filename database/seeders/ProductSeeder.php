<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $products = [
            ['name' => 'Wireless Bluetooth Headphones', 'description' => 'Over-ear noise cancelling headphones with 30-hour battery life and deep bass.', 'price' => 129.99, 'stock' => 50],
            ['name' => 'Smart Fitness Watch', 'description' => 'Track your heart rate, steps, sleep, and workouts with a bright AMOLED display.', 'price' => 89.99, 'stock' => 75],
            ['name' => 'Portable Bluetooth Speaker', 'description' => 'Waterproof speaker with 360-degree sound and 12 hours of playtime.', 'price' => 45.50, 'stock' => 120],
            ['name' => 'Mechanical Gaming Keyboard', 'description' => 'RGB backlit keyboard with tactile blue switches and anti-ghosting keys.', 'price' => 69.99, 'stock' => 60],
            ['name' => 'Wireless Optical Mouse', 'description' => 'Ergonomic mouse with adjustable DPI and silent click buttons.', 'price' => 24.99, 'stock' => 200],
            ['name' => '4K Ultra HD Monitor', 'description' => '27-inch IPS display with HDR support and ultra-thin bezels.', 'price' => 299.00, 'stock' => 35],
            ['name' => 'USB-C Fast Charger', 'description' => '65W GaN charger with dual ports for laptops and phones.', 'price' => 34.99, 'stock' => 150],
            ['name' => 'Laptop Backpack', 'description' => 'Water-resistant backpack with padded compartment for 15-inch laptops.', 'price' => 49.99, 'stock' => 90],
            ['name' => 'Webcam Full HD 1080p', 'description' => 'Plug-and-play webcam with built-in microphone for video calls.', 'price' => 39.99, 'stock' => 80],
            ['name' => 'Power Bank 20000mAh', 'description' => 'High-capacity portable charger with fast charging and dual USB output.', 'price' => 29.99, 'stock' => 140],
            ['name' => 'Smartphone Stand Holder', 'description' => 'Adjustable aluminium desk stand compatible with all phones and tablets.', 'price' => 15.99, 'stock' => 250],
            ['name' => 'Noise Cancelling Earbuds', 'description' => 'True wireless earbuds with active noise cancellation and charging case.', 'price' => 79.99, 'stock' => 110],
            ['name' => 'LED Desk Lamp', 'description' => 'Dimmable lamp with touch control, USB charging port, and 3 color modes.', 'price' => 27.50, 'stock' => 95],
            ['name' => 'External SSD 1TB', 'description' => 'Portable solid-state drive with USB 3.2 and read speeds up to 1050MB/s.', 'price' => 119.00, 'stock' => 45],
            ['name' => 'Wireless Charging Pad', 'description' => 'Fast 15W Qi wireless charger compatible with all modern smartphones.', 'price' => 22.99, 'stock' => 160],
            ['name' => 'Gaming Headset', 'description' => 'Surround sound headset with noise-isolating microphone and comfort earcups.', 'price' => 59.99, 'stock' => 70],
            ['name' => 'Smart LED Light Bulb', 'description' => 'WiFi-enabled color changing bulb that works with voice assistants.', 'price' => 12.99, 'stock' => 300],
            ['name' => 'Tablet 10 inch', 'description' => 'Android tablet with 64GB storage, octa-core processor, and HD display.', 'price' => 179.00, 'stock' => 40],
            ['name' => 'Action Camera 4K', 'description' => 'Waterproof action camera with image stabilization and wide-angle lens.', 'price' => 99.99, 'stock' => 55],
            ['name' => 'Ergonomic Office Chair', 'description' => 'Adjustable mesh chair with lumbar support and breathable backrest.', 'price' => 159.99, 'stock' => 30],
        ];

        foreach ($products as $product) {
            Product::create([
                ...$product,
                'image_path' => null,
                'is_active' => true,
            ]);
        }
    }
}
