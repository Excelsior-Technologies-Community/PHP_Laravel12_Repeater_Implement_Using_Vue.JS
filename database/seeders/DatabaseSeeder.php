<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFaq;
use App\Models\ProductHighlight;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            ['name' => 'Footwear & Sneakers', 'description' => 'Athletic running shoes, luxury sneakers, and casual boots.'],
            ['name' => 'Smart Electronics', 'description' => 'Flagship smartphones, smartwatches, earbuds, and audio gear.'],
            ['name' => 'Apparel & Fashion', 'description' => 'Premium cotton t-shirts, jackets, hoodies, and streetwear.'],
            ['name' => 'Gaming & Accessories', 'description' => 'High-end gaming peripherals, mechanical keyboards, and headsets.'],
            ['name' => 'Travel & Backpacks', 'description' => 'Weatherproof backpacks, luggage bags, and travel essentials.'],
            ['name' => 'Fitness & Sports', 'description' => 'Workout apparel, gym gear, mats, and outdoor sports accessories.'],
        ];

        $categoryModels = [];
        foreach ($categoriesData as $cat) {
            $categoryModels[] = Category::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'description' => $cat['description']]
            );
        }

        // 2. Seed Brands
        $brandsData = [
            'Nike Athletic',
            'Apple Innovation',
            'Sony Audio & Tech',
            'Razer Gaming',
            'Patagonia Outwear',
            'Adidas Originals',
        ];

        $brandModels = [];
        foreach ($brandsData as $bName) {
            $brandModels[] = Brand::firstOrCreate(
                ['name' => $bName],
                ['slug' => Str::slug($bName)]
            );
        }

        // 3. Seed 10 Rich Sample Products
        $productsSeed = [
            [
                'title' => 'Nike Air Zoom Pegasus 41 Performance Running Shoes',
                'sku' => 'NIKE-PEG41-PRO',
                'price' => 135.00,
                'status' => 'active',
                'description' => 'Responsive cushioning in the Pegasus provides an energized ride for everyday road running. Experience lighter-weight energy return with dual Air Zoom units and a ReactX foam midsole.',
                'stock_quantity' => 85,
                'low_stock_threshold' => 15,
                'category_idx' => 0,
                'brand_idx' => 0,
                'variants' => [
                    ['size' => 'US 8', 'color' => 'Black / White', 'sku' => 'PEG41-BLK-8', 'price' => 135.00, 'stock_quantity' => 20],
                    ['size' => 'US 9', 'color' => 'Black / White', 'sku' => 'PEG41-BLK-9', 'price' => 135.00, 'stock_quantity' => 25],
                    ['size' => 'US 10', 'color' => 'Volt / Glacier Blue', 'sku' => 'PEG41-BLU-10', 'price' => 140.00, 'stock_quantity' => 18],
                    ['size' => 'US 11', 'color' => 'Pure Platinum', 'sku' => 'PEG41-PLT-11', 'price' => 135.00, 'stock_quantity' => 22],
                ],
                'specs' => [
                    ['spec_key' => 'Midsole Technology', 'spec_value' => 'ReactX Foam + Dual Air Zoom'],
                    ['spec_key' => 'Weight', 'spec_value' => '297g (Men size 10)'],
                    ['spec_key' => 'Heel-to-Toe Drop', 'spec_value' => '10mm'],
                    ['spec_key' => 'Terrain', 'spec_value' => 'Road, Track, Pavement'],
                    ['spec_key' => 'Upper Material', 'spec_value' => 'Engineered Breathable Mesh'],
                ],
                'faqs' => [
                    ['question' => 'Are these shoes suitable for marathon training?', 'answer' => 'Yes, the Pegasus 41 is engineered with long-distance comfort and durability suitable for full marathon preparations.'],
                    ['question' => 'How do these fit compared to Pegasus 40?', 'answer' => 'They fit true to size with a slightly roomier midfoot toe-box for swollen feet during long runs.'],
                    ['question' => 'Is this model waterproof?', 'answer' => 'No, this standard edition is engineered for high breathability. For rain, consider the Pegasus Shield variant.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Upgraded breathable engineered mesh upper'],
                    ['highlight_text' => 'ReactX foam midsole surrounds forefoot and heel Air Zoom units'],
                    ['highlight_text' => 'Signature waffle-inspired rubber outsole for traction and flexibility'],
                    ['highlight_text' => 'Plush collar, tongue and sockliner for a secure, comfortable fit'],
                ],
            ],
            [
                'title' => 'Apple iPhone 16 Pro Max 256GB - Titanium Finish',
                'sku' => 'APL-IP16PM-256',
                'price' => 1199.00,
                'status' => 'active',
                'description' => 'Featuring a stunning titanium design, Camera Control, 4K 120 fps Dolby Vision, and the revolutionary A18 Pro chip for unparalleled mobile performance.',
                'stock_quantity' => 42,
                'low_stock_threshold' => 10,
                'category_idx' => 1,
                'brand_idx' => 1,
                'variants' => [
                    ['size' => '256 GB', 'color' => 'Desert Titanium', 'sku' => 'IP16PM-DES-256', 'price' => 1199.00, 'stock_quantity' => 12],
                    ['size' => '256 GB', 'color' => 'Natural Titanium', 'sku' => 'IP16PM-NAT-256', 'price' => 1199.00, 'stock_quantity' => 15],
                    ['size' => '512 GB', 'color' => 'Black Titanium', 'sku' => 'IP16PM-BLK-512', 'price' => 1399.00, 'stock_quantity' => 10],
                    ['size' => '1 TB', 'color' => 'White Titanium', 'sku' => 'IP16PM-WHT-1TB', 'price' => 1599.00, 'stock_quantity' => 5],
                ],
                'specs' => [
                    ['spec_key' => 'Processor', 'spec_value' => 'Apple A18 Pro with 6-core GPU'],
                    ['spec_key' => 'Display', 'spec_value' => '6.9-inch Super Retina XDR OLED 120Hz'],
                    ['spec_key' => 'Main Camera', 'spec_value' => '48MP Fusion + 48MP Ultra Wide + 5x Telephoto'],
                    ['spec_key' => 'Battery Life', 'spec_value' => 'Up to 33 hours video playback'],
                    ['spec_key' => 'Water Resistance', 'spec_value' => 'IP68 (6 meters up to 30 mins)'],
                ],
                'faqs' => [
                    ['question' => 'Does it include the USB-C charging cable in the box?', 'answer' => 'Yes, a 1-meter braided USB-C Charge Cable is included in the package.'],
                    ['question' => 'What is the new Camera Control button?', 'answer' => 'It is a capacitive tactile button that allows instant zoom, exposure, and aperture adjustments with haptic feedback.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Strong and lightweight Grade 5 titanium design with micro-blasted texture'],
                    ['highlight_text' => 'Largest 6.9-inch Super Retina XDR display with ultra-thin borders'],
                    ['highlight_text' => 'Next-generation Photographic Styles and spatial audio recording'],
                    ['highlight_text' => 'Fast charging up to 50% in approximately 30 minutes with 30W adapter'],
                ],
            ],
            [
                'title' => 'Sony WH-1000XM5 Wireless Noise-Canceling Headphones',
                'sku' => 'SNY-WH1000XM5-SLV',
                'price' => 398.00,
                'status' => 'active',
                'description' => 'Industry-leading noise cancellation with two processors and 8 microphones. Magnificent Hi-Res sound quality and ultra-comfortable lightweight design.',
                'stock_quantity' => 60,
                'low_stock_threshold' => 12,
                'category_idx' => 1,
                'brand_idx' => 2,
                'variants' => [
                    ['size' => 'Standard', 'color' => 'Midnight Black', 'sku' => 'XM5-BLK', 'price' => 398.00, 'stock_quantity' => 30],
                    ['size' => 'Standard', 'color' => 'Platinum Silver', 'sku' => 'XM5-SLV', 'price' => 398.00, 'stock_quantity' => 20],
                    ['size' => 'Standard', 'color' => 'Smoky Pink', 'sku' => 'XM5-PNK', 'price' => 418.00, 'stock_quantity' => 10],
                ],
                'specs' => [
                    ['spec_key' => 'Noise Cancellation', 'spec_value' => 'Auto NC Optimizer with Integrated Processor V1 + QN1'],
                    ['spec_key' => 'Battery Life', 'spec_value' => 'Up to 30 hours (ANC On), 40 hours (ANC Off)'],
                    ['spec_key' => 'Bluetooth Version', 'spec_value' => 'v5.2 with LDAC & Multipoint connection'],
                    ['spec_key' => 'Driver Unit', 'spec_value' => '30mm Carbon fiber composite dome'],
                    ['spec_key' => 'Weight', 'spec_value' => '250 grams'],
                ],
                'faqs' => [
                    ['question' => 'Can I connect these to my laptop and phone at the same time?', 'answer' => 'Yes, multipoint pairing allows you to stay connected to two Bluetooth devices simultaneously.'],
                    ['question' => 'Does it charge quickly?', 'answer' => 'Yes, a 3-minute quick charge using a USB-PD compatible adapter provides up to 3 hours of playback.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Two processors control 8 microphones for unprecedented noise cancellation'],
                    ['highlight_text' => 'Crystal clear hands-free calling with 4 beamforming microphones & AI noise reduction'],
                    ['highlight_text' => 'Ultra-comfortable, lightweight design with soft fit leather headband'],
                    ['highlight_text' => 'Intuitive touch sensor controls to pause, skip tracks, and adjust volume'],
                ],
            ],
            [
                'title' => 'Razer BlackWidow V4 Pro Mechanical Gaming Keyboard',
                'sku' => 'RZR-BW-V4PRO',
                'price' => 229.99,
                'status' => 'active',
                'description' => 'Full-blown battlestation immersion with Razer Command Dial, 8 dedicated macro keys, true 8000Hz polling rate, and vibrant Chroma RGB underglow.',
                'stock_quantity' => 35,
                'low_stock_threshold' => 8,
                'category_idx' => 3,
                'brand_idx' => 3,
                'variants' => [
                    ['size' => 'Full Size', 'color' => 'Green Clicky Switches', 'sku' => 'BW4-GRN', 'price' => 229.99, 'stock_quantity' => 18],
                    ['size' => 'Full Size', 'color' => 'Yellow Linear Switches', 'sku' => 'BW4-YLW', 'price' => 229.99, 'stock_quantity' => 17],
                ],
                'specs' => [
                    ['spec_key' => 'Switch Type', 'spec_value' => 'Razer Gen-3 Mechanical Switches (100M Keystroke lifespan)'],
                    ['spec_key' => 'Polling Rate', 'spec_value' => 'Up to 8000Hz HyperPolling'],
                    ['spec_key' => 'Keycaps', 'spec_value' => 'Doubleshot ABS Keycaps'],
                    ['spec_key' => 'Wrist Rest', 'spec_value' => 'Plush Leatherette with Magnetic RGB Underglow'],
                    ['spec_key' => 'Connectivity', 'spec_value' => 'Detachable Type-C with USB 2.0 Passthrough'],
                ],
                'faqs' => [
                    ['question' => 'What does the Razer Command Dial do?', 'answer' => 'It allows you to assign custom commands like keyboard brightness, zoom, track scrubbing, and app switching.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Dedicated macro keys and customizable Command Dial'],
                    ['highlight_text' => '3-side keyboard underglow and per-key RGB illumination'],
                    ['highlight_text' => 'Lubricated stabilizers and 2 layers of sound dampening foam inside'],
                ],
            ],
            [
                'title' => 'Patagonia Black Hole MLC 45L Weatherproof Travel Backpack',
                'sku' => 'PAT-BH-MLC45',
                'price' => 239.00,
                'status' => 'active',
                'description' => 'A burly, soft-sided 45-liter travel pack that converts to a duffel or shoulder bag with ample room for world travel and 100% recycled fabrics.',
                'stock_quantity' => 28,
                'low_stock_threshold' => 6,
                'category_idx' => 4,
                'brand_idx' => 4,
                'variants' => [
                    ['size' => '45 Liters', 'color' => 'Black', 'sku' => 'MLC45-BLK', 'price' => 239.00, 'stock_quantity' => 10],
                    ['size' => '45 Liters', 'color' => 'Smolder Blue', 'sku' => 'MLC45-BLU', 'price' => 239.00, 'stock_quantity' => 10],
                    ['size' => '45 Liters', 'color' => 'Sediment Earth', 'sku' => 'MLC45-SED', 'price' => 249.00, 'stock_quantity' => 8],
                ],
                'specs' => [
                    ['spec_key' => 'Capacity', 'spec_value' => '45 Liters (Meets strict airline carry-on requirements)'],
                    ['spec_key' => 'Material', 'spec_value' => '100% Recycled Polyester Ripstop with TPU laminate'],
                    ['spec_key' => 'Laptop Sleeve', 'spec_value' => 'Dedicated TSA-approved clamshell fits up to 16-inch laptops'],
                    ['spec_key' => 'Weight', 'spec_value' => '1,630 grams'],
                ],
                'faqs' => [
                    ['question' => 'Is this carry-on compliant for domestic and international flights?', 'answer' => 'Yes, it meets the majority of airline maximum carry-on dimension guidelines.'],
                    ['question' => 'Can the backpack straps be tucked away?', 'answer' => 'Yes, the padded shoulder straps stow completely into a zippered back panel for duffel mode.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Extremely durable, weather-resistant 100% recycled ripstop fabric'],
                    ['highlight_text' => 'Versatile 3-in-1 carry system: Backpack, shoulder bag, or briefcase'],
                    ['highlight_text' => 'Roll-top style secondary compartments for dirty laundry segregation'],
                    ['highlight_text' => 'Fair Trade Certified sewn and environmental bluesign approved'],
                ],
            ],
            [
                'title' => 'Adidas Ultraboost Light Carbon Running Shoes',
                'sku' => 'ADI-UB-LIGHT',
                'price' => 190.00,
                'status' => 'active',
                'description' => 'Experience epic energy with the lightest Ultraboost ever made. Built with 30% lighter Light BOOST material for maximum comfort on every stride.',
                'stock_quantity' => 50,
                'low_stock_threshold' => 10,
                'category_idx' => 0,
                'brand_idx' => 5,
                'variants' => [
                    ['size' => 'UK 7.5', 'color' => 'Core Black / Cloud White', 'sku' => 'UBL-BLK-75', 'price' => 190.00, 'stock_quantity' => 15],
                    ['size' => 'UK 8.5', 'color' => 'Core Black / Cloud White', 'sku' => 'UBL-BLK-85', 'price' => 190.00, 'stock_quantity' => 15],
                    ['size' => 'UK 9.5', 'color' => 'Solar Red / Flash Orange', 'sku' => 'UBL-RED-95', 'price' => 195.00, 'stock_quantity' => 20],
                ],
                'specs' => [
                    ['spec_key' => 'Weight', 'spec_value' => '293g (Size 8.5)'],
                    ['spec_key' => 'Cushioning', 'spec_value' => 'Light BOOST Capsule Midsole'],
                    ['spec_key' => 'Outsole', 'spec_value' => 'Continental Better Rubber for all-weather grip'],
                    ['spec_key' => 'Upper', 'spec_value' => 'PRIMEKNIT+ Forged textile made with 50% ocean plastic'],
                ],
                'faqs' => [
                    ['question' => 'Is this lighter than previous Ultraboost versions?', 'answer' => 'Yes, Light BOOST represents a 30% weight reduction compared to standard Boost foam.'],
                ],
                'highlights' => [
                    ['highlight_text' => '30% lighter BOOST foam material with superior energy return'],
                    ['highlight_text' => 'Linear Energy Push (LEP) system tuned for stiffer toe-off transition'],
                    ['highlight_text' => 'Continental Rubber outsole ensures unshakeable grip on wet surfaces'],
                ],
            ],
            [
                'title' => 'Patagonia Better Sweater Fleece Quarter-Zip Pullover',
                'sku' => 'PAT-BTSWT-QZ',
                'price' => 129.00,
                'status' => 'active',
                'description' => 'A warm, low-bulk quarter-zip pullover made with soft 100% recycled polyester sweater-knit fleece dyed with a low-impact process.',
                'stock_quantity' => 45,
                'low_stock_threshold' => 10,
                'category_idx' => 2,
                'brand_idx' => 4,
                'variants' => [
                    ['size' => 'Small', 'color' => 'Stonewash Heather', 'sku' => 'BSW-S-SWH', 'price' => 129.00, 'stock_quantity' => 10],
                    ['size' => 'Medium', 'color' => 'Stonewash Heather', 'sku' => 'BSW-M-SWH', 'price' => 129.00, 'stock_quantity' => 15],
                    ['size' => 'Large', 'color' => 'Nickel with Forge Grey', 'sku' => 'BSW-L-NCK', 'price' => 129.00, 'stock_quantity' => 12],
                    ['size' => 'XL', 'color' => 'New Navy', 'sku' => 'BSW-XL-NVY', 'price' => 134.00, 'stock_quantity' => 8],
                ],
                'specs' => [
                    ['spec_key' => 'Fabric', 'spec_value' => '10-oz 100% recycled polyester knitted fleece'],
                    ['spec_key' => 'Zipper', 'spec_value' => 'Quarter-length reverse-coil zipper with stand-up collar'],
                    ['spec_key' => 'Pockets', 'spec_value' => 'Zippered security pocket on left chest'],
                    ['spec_key' => 'Fit', 'spec_value' => 'Slim, body-contouring active fit'],
                ],
                'faqs' => [
                    ['question' => 'How should I wash this fleece to avoid pilling?', 'answer' => 'Machine wash cold on gentle cycle, tumble dry low or hang dry.'],
                ],
                'highlights' => [
                    ['highlight_text' => '100% recycled polyester sweater-knit fleece exterior with brushed interior'],
                    ['highlight_text' => 'Raglan sleeve construction for mobility and pack-wearing comfort'],
                    ['highlight_text' => 'Shape-holding micropolyester-jersey trim at cuffs and bottom hem'],
                ],
            ],
            [
                'title' => 'Sony PlayStation 5 DualSense Edge Wireless Controller',
                'sku' => 'SNY-DS-EDGE',
                'price' => 199.99,
                'status' => 'active',
                'description' => 'Built with high performance and personalization in mind, this controller invites you to craft your own unique gaming experience.',
                'stock_quantity' => 22,
                'low_stock_threshold' => 5,
                'category_idx' => 3,
                'brand_idx' => 2,
                'variants' => [
                    ['size' => 'Standard Edition', 'color' => 'Signature White/Black', 'sku' => 'DSEDGE-WHT', 'price' => 199.99, 'stock_quantity' => 22],
                ],
                'specs' => [
                    ['spec_key' => 'Thumbstick Modules', 'spec_value' => 'Replaceable stick modules (swappable in seconds)'],
                    ['spec_key' => 'Back Buttons', 'spec_value' => '2 sets of swappable back buttons (Half-dome & Lever)'],
                    ['spec_key' => 'Triggers', 'spec_value' => 'Adjustable trigger travel stops & deadzones'],
                    ['spec_key' => 'Battery', 'spec_value' => 'Rechargeable 1050mAh lithium-ion'],
                ],
                'faqs' => [
                    ['question' => 'Does it include the carrying case?', 'answer' => 'Yes, a hard-shell protective travel case that charges the controller inside is included.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Customizable controls, remappable buttons, and stick sensitivity profiles'],
                    ['highlight_text' => 'Haptic feedback and adaptive triggers for unmatched game realism'],
                    ['highlight_text' => 'Lockable braided USB cable preventing accidental disconnection'],
                ],
            ],
            [
                'title' => 'Apple Watch Ultra 2 GPS + Cellular 49mm Titanium Case',
                'sku' => 'APL-WAT-ULT2',
                'price' => 799.00,
                'status' => 'active',
                'description' => 'The ultimate sports and adventure watch. Powered by S9 SiP, 3000-nit display, precision dual-frequency GPS, and up to 72 hours of battery in Low Power Mode.',
                'stock_quantity' => 18,
                'low_stock_threshold' => 5,
                'category_idx' => 1,
                'brand_idx' => 1,
                'variants' => [
                    ['size' => '49mm - Small/Med', 'color' => 'Natural Titanium / Orange Ocean Band', 'sku' => 'WAT-ULT2-OCN', 'price' => 799.00, 'stock_quantity' => 8],
                    ['size' => '49mm - Medium/Lrg', 'color' => 'Natural Titanium / Blue Alpine Loop', 'sku' => 'WAT-ULT2-ALP', 'price' => 799.00, 'stock_quantity' => 10],
                ],
                'specs' => [
                    ['spec_key' => 'Case Size & Material', 'spec_value' => '49mm Aerospace-grade Titanium with Sapphire crystal face'],
                    ['spec_key' => 'Peak Brightness', 'spec_value' => '3000 nits Always-On Retina Display'],
                    ['spec_key' => 'Water Resistance', 'spec_value' => '100m water resistant, EN13319 certified dive computer to 40m'],
                    ['spec_key' => 'Siren', 'spec_value' => '86-decibel sound siren audible up to 180 meters away'],
                ],
                'faqs' => [
                    ['question' => 'Can I use this for scuba diving?', 'answer' => 'Yes, with the Oceanic+ app it functions as a fully capable dive computer down to 40 meters.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'Rugged aerospace-grade titanium case with raised bezel protecting sapphire glass'],
                    ['highlight_text' => 'Customizable Action button for immediate workout start or compass waypoint drop'],
                    ['highlight_text' => 'Dual-frequency GPS (L1 and L5) providing pinpoint accuracy in dense cities'],
                ],
            ],
            [
                'title' => 'Nike Dri-FIT Repel Heritage Trail Running Jacket',
                'sku' => 'NIKE-TRL-JKT',
                'price' => 110.00,
                'status' => 'inactive',
                'description' => 'Lightweight, water-repellent coverage built for off-road trails and rainy runs. Packable into its own pocket with vintage chevron windrunner styling.',
                'stock_quantity' => 15,
                'low_stock_threshold' => 4,
                'category_idx' => 5,
                'brand_idx' => 0,
                'variants' => [
                    ['size' => 'M', 'color' => 'Cargo Khaki / Mineral Teal', 'sku' => 'TRL-JKT-M-KHK', 'price' => 110.00, 'stock_quantity' => 8],
                    ['size' => 'L', 'color' => 'Cargo Khaki / Mineral Teal', 'sku' => 'TRL-JKT-L-KHK', 'price' => 110.00, 'stock_quantity' => 7],
                ],
                'specs' => [
                    ['spec_key' => 'Material', 'spec_value' => '100% Recycled woven polyester with DWR water-repellent finish'],
                    ['spec_key' => 'Pockets', 'spec_value' => '2 zip hand pockets + 1 packable back stow pocket'],
                    ['spec_key' => 'Hood', 'spec_value' => '3-panel bungee adjustable hood with brim'],
                ],
                'faqs' => [
                    ['question' => 'Does this jacket pack into itself?', 'answer' => 'Yes, it packs into the back zippered pocket and comes with a carrying strap.'],
                ],
                'highlights' => [
                    ['highlight_text' => 'DWR water-repellent finish keeps you dry in damp weather'],
                    ['highlight_text' => 'Vented back yoke increases airflow when your run heats up'],
                    ['highlight_text' => 'Reflective Trail graphics enhance low-light visibility'],
                ],
            ],
        ];

        foreach ($productsSeed as $pData) {
            $catId = $categoryModels[$pData['category_idx']]->id ?? null;
            $brandId = $brandModels[$pData['brand_idx']]->id ?? null;

            $product = Product::create([
                'title' => $pData['title'],
                'sku' => $pData['sku'],
                'price' => $pData['price'],
                'status' => $pData['status'],
                'description' => $pData['description'],
                'stock_quantity' => $pData['stock_quantity'],
                'low_stock_threshold' => $pData['low_stock_threshold'],
                'category_id' => $catId,
                'brand_id' => $brandId,
            ]);

            // Create Variants
            if (!empty($pData['variants'])) {
                foreach ($pData['variants'] as $var) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $var['size'],
                        'color' => $var['color'],
                        'sku' => $var['sku'],
                        'price' => $var['price'],
                        'stock_quantity' => $var['stock_quantity'],
                    ]);
                }
            }

            // Create Specs
            if (!empty($pData['specs'])) {
                foreach ($pData['specs'] as $idx => $spec) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'spec_key' => $spec['spec_key'],
                        'spec_value' => $spec['spec_value'],
                        'sort_order' => $idx + 1,
                    ]);
                }
            }

            // Create FAQs
            if (!empty($pData['faqs'])) {
                foreach ($pData['faqs'] as $idx => $faq) {
                    ProductFaq::create([
                        'product_id' => $product->id,
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                        'sort_order' => $idx + 1,
                    ]);
                }
            }

            // Create Highlights
            if (!empty($pData['highlights'])) {
                foreach ($pData['highlights'] as $idx => $hl) {
                    ProductHighlight::create([
                        'product_id' => $product->id,
                        'highlight_text' => $hl['highlight_text'],
                        'sort_order' => $idx + 1,
                    ]);
                }
            }
        }
    }
}
