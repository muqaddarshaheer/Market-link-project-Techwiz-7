<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Category;
use App\Models\ChatbotFaq;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // —— Demo accounts ——
        $admin = User::create([
            'name' => 'MarketLink Admin',
            'email' => 'admin@marketlink.com',
            'phone' => '555-0100',
            'address' => '1 Admin Plaza',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $farmerUser = User::create([
            'name' => 'Green Valley Farmer',
            'email' => 'farmer@marketlink.com',
            'phone' => '555-0200',
            'address' => '42 Orchard Lane',
            'password' => Hash::make('Farmer@123'),
            'role' => 'farmer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $customer = User::create([
            'name' => 'Demo Customer',
            'email' => 'customer@marketlink.com',
            'phone' => '555-0300',
            'address' => '88 Maple Street',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        Cart::create(['customer_id' => $customer->id]);

        // Extra farmers
        $farmer2User = User::create([
            'name' => 'Sunrise Dairy',
            'email' => 'sunrise@marketlink.com',
            'phone' => '555-0201',
            'password' => Hash::make('Farmer@123'),
            'role' => 'farmer',
            'status' => 'active',
        ]);
        $farmer3User = User::create([
            'name' => 'Herb Haven',
            'email' => 'herbs@marketlink.com',
            'phone' => '555-0202',
            'password' => Hash::make('Farmer@123'),
            'role' => 'farmer',
            'status' => 'active',
        ]);

        $farmer = FarmerProfile::create([
            'user_id' => $farmerUser->id,
            'stall_name' => 'Green Valley Produce',
            'business_description' => 'Family-grown vegetables and seasonal fruits from our valley farm.',
            'contact_person' => 'Green Valley Farmer',
            'operating_days' => ['Wednesday', 'Saturday'],
            'address' => '42 Orchard Lane, Brooklyn, NY',
            'latitude' => 40.6782,
            'longitude' => -73.9442,
            'approval_status' => 'approved',
        ]);

        $farmer2 = FarmerProfile::create([
            'user_id' => $farmer2User->id,
            'stall_name' => 'Sunrise Dairy Co-op',
            'business_description' => 'Fresh milk, cheese, eggs, and honey from pasture-raised animals.',
            'contact_person' => 'Maria Santos',
            'operating_days' => ['Saturday', 'Sunday'],
            'address' => '15 Creamery Rd, Queens, NY',
            'latitude' => 40.7282,
            'longitude' => -73.7949,
            'approval_status' => 'approved',
        ]);

        $farmer3 = FarmerProfile::create([
            'user_id' => $farmer3User->id,
            'stall_name' => 'Herb Haven',
            'business_description' => 'Culinary herbs, bunches, and baked goods made same-morning.',
            'contact_person' => 'James Chen',
            'operating_days' => ['Friday', 'Saturday'],
            'address' => '9 Greenhouse Way, Manhattan, NY',
            'latitude' => 40.7580,
            'longitude' => -73.9855,
            'approval_status' => 'approved',
        ]);

        // —— Markets ——
        $markets = collect([
            ['name' => 'Brooklyn Greenmarket', 'address' => 'Grand Army Plaza', 'city' => 'Brooklyn', 'operating_days' => ['Saturday'], 'opening_time' => '08:00', 'closing_time' => '15:00', 'latitude' => 40.6730, 'longitude' => -73.9700, 'description' => 'Weekend market with local produce under the trees.'],
            ['name' => 'Union Square Farmers Market', 'address' => 'E 17th St & Union Square W', 'city' => 'Manhattan', 'operating_days' => ['Monday', 'Wednesday', 'Friday', 'Saturday'], 'opening_time' => '08:00', 'closing_time' => '18:00', 'latitude' => 40.7359, 'longitude' => -73.9911, 'description' => 'Iconic year-round market in the heart of the city.'],
            ['name' => 'Queens Fresh Fair', 'address' => 'Downtown Flushing Plaza', 'city' => 'Queens', 'operating_days' => ['Sunday'], 'opening_time' => '09:00', 'closing_time' => '14:00', 'latitude' => 40.7590, 'longitude' => -73.8300, 'description' => 'Community fair featuring dairy, eggs, and greens.'],
            ['name' => 'Hudson River Market', 'address' => 'Pier 57 Riverside', 'city' => 'Manhattan', 'operating_days' => ['Thursday', 'Saturday'], 'opening_time' => '10:00', 'closing_time' => '16:00', 'latitude' => 40.7430, 'longitude' => -74.0080, 'description' => 'Waterfront stalls with herbs and baked goods.'],
            ['name' => 'Prospect Park Todmorden', 'address' => 'Near Lincoln Rd entrance', 'city' => 'Brooklyn', 'operating_days' => ['Wednesday'], 'opening_time' => '08:00', 'closing_time' => '14:00', 'latitude' => 40.6602, 'longitude' => -73.9690, 'description' => 'Midweek market for greens and pantry staples.'],
        ])->map(fn ($data) => Market::create($data + ['status' => 'active', 'map_provider' => 'OpenStreetMap']));

        $farmer->markets()->attach($markets[0]->id, ['stall_number' => 'A12', 'operating_day' => 'Saturday', 'pickup_notes' => 'Pickup at stall A12 near the fountain.']);
        $farmer->markets()->attach($markets[1]->id, ['stall_number' => 'B4', 'operating_day' => 'Wednesday', 'pickup_notes' => 'Look for the green canopy.']);
        $farmer2->markets()->attach($markets[2]->id, ['stall_number' => 'D2', 'operating_day' => 'Sunday']);
        $farmer2->markets()->attach($markets[1]->id, ['stall_number' => 'C1', 'operating_day' => 'Saturday']);
        $farmer3->markets()->attach($markets[3]->id, ['stall_number' => 'H7', 'operating_day' => 'Saturday']);
        $farmer3->markets()->attach($markets[4]->id, ['stall_number' => 'T3', 'operating_day' => 'Wednesday']);

        // —— Categories ——
        $categoryData = [
            ['name' => 'Vegetables', 'icon' => 'bi-carrot', 'description' => 'Leafy greens and garden vegetables'],
            ['name' => 'Fruits', 'icon' => 'bi-apple', 'description' => 'Seasonal orchard fruits'],
            ['name' => 'Dairy', 'icon' => 'bi-cup-straw', 'description' => 'Milk, cheese, yogurt'],
            ['name' => 'Baked Goods', 'icon' => 'bi-basket2', 'description' => 'Fresh breads and pastries'],
            ['name' => 'Herbs', 'icon' => 'bi-flower1', 'description' => 'Culinary and medicinal herbs'],
            ['name' => 'Eggs', 'icon' => 'bi-egg', 'description' => 'Farm-fresh eggs'],
            ['name' => 'Honey', 'icon' => 'bi-droplet', 'description' => 'Local raw honey'],
        ];
        $categories = collect($categoryData)->mapWithKeys(fn ($c) => [$c['name'] => Category::create($c)]);

        // —— Products (18+) ——
        $productsSeed = [
            [$farmer, $markets[0], 'Vegetables', 'Heirloom Tomatoes', 4.50, 'kg', 40, true, 'Sun-ripened mixed heirloom tomatoes.'],
            [$farmer, $markets[0], 'Vegetables', 'Baby Spinach', 3.25, 'bunch', 30, true, 'Tender baby spinach bunches.'],
            [$farmer, $markets[1], 'Vegetables', 'Rainbow Carrots', 2.75, 'kg', 50, false, 'Sweet rainbow carrots, scrubbed clean.'],
            [$farmer, $markets[1], 'Fruits', 'Honeycrisp Apples', 3.99, 'kg', 60, true, 'Crisp autumn apples.'],
            [$farmer, $markets[0], 'Fruits', 'Blueberries', 5.50, 'pack', 25, false, 'Pint packs of local blueberries.'],
            [$farmer, $markets[0], 'Vegetables', 'Zucchini', 2.20, 'kg', 35, false, 'Firm summer squash.'],
            [$farmer2, $markets[2], 'Dairy', 'Fresh Whole Milk', 4.00, 'litre', 40, true, 'Non-homogenized whole milk.'],
            [$farmer2, $markets[2], 'Dairy', 'Farmhouse Cheddar', 8.50, 'piece', 20, false, 'Aged 6-month cheddar wedges.'],
            [$farmer2, $markets[1], 'Eggs', 'Free-Range Eggs', 6.00, 'dozen', 45, true, 'Pasture-raised large eggs.'],
            [$farmer2, $markets[1], 'Honey', 'Wildflower Honey', 9.00, 'pack', 18, true, 'Raw wildflower honey jars.'],
            [$farmer2, $markets[2], 'Dairy', 'Cultured Butter', 5.75, 'pack', 22, false, 'Cultured butter, lightly salted.'],
            [$farmer3, $markets[3], 'Herbs', 'Basil Bunch', 2.50, 'bunch', 40, true, 'Fragrant Genovese basil.'],
            [$farmer3, $markets[3], 'Herbs', 'Rosemary', 2.00, 'bunch', 28, false, 'Woody rosemary sprigs.'],
            [$farmer3, $markets[3], 'Baked Goods', 'Sourdough Loaf', 7.00, 'piece', 15, true, 'Naturally leavened sourdough.'],
            [$farmer3, $markets[4], 'Baked Goods', 'Berry Muffins', 4.50, 'pack', 20, false, 'Pack of 4 berry muffins.'],
            [$farmer3, $markets[4], 'Herbs', 'Mint', 1.75, 'bunch', 32, false, 'Cooling spearmint bunches.'],
            [$farmer, $markets[1], 'Vegetables', 'Kale', 2.40, 'bunch', 5, false, 'Curly kale — low stock.'],
            [$farmer2, $markets[1], 'Eggs', 'Duck Eggs', 8.00, 'dozen', 12, false, 'Rich duck eggs, limited supply.'],
        ];

        $products = [];
        foreach ($productsSeed as [$fp, $market, $catName, $name, $price, $unit, $stock, $featured, $desc]) {
            $products[] = Product::create([
                'farmer_id' => $fp->id,
                'market_id' => $market->id,
                'category_id' => $categories[$catName]->id,
                'name' => $name,
                'description' => $desc,
                'price' => $price,
                'unit' => $unit,
                'stock_quantity' => $stock,
                'is_available' => true,
                'is_sold_out' => false,
                'is_featured' => $featured,
                'weekly_stock_template' => $stock,
                'views_count' => rand(5, 120),
            ]);
        }

        // —— Sample completed order + reviews ——
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customer->id,
            'farmer_id' => $farmer->id,
            'market_id' => $markets[0]->id,
            'pickup_date' => now()->subDays(3)->toDateString(),
            'pickup_slot' => '10:00-12:00',
            'status' => 'completed',
            'total_amount' => 15.50,
            'customer_note' => 'Please include a paper bag.',
            'cutoff_time' => now()->subDays(4),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $products[0]->id,
            'product_name' => $products[0]->name,
            'unit_price' => $products[0]->price,
            'quantity' => 2,
            'subtotal' => $products[0]->price * 2,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $products[1]->id,
            'product_name' => $products[1]->name,
            'unit_price' => $products[1]->price,
            'quantity' => 2,
            'subtotal' => $products[1]->price * 2,
        ]);

        $activeOrder = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => $customer->id,
            'farmer_id' => $farmer2->id,
            'market_id' => $markets[1]->id,
            'pickup_date' => now()->addDays(2)->toDateString(),
            'pickup_slot' => '12:00-14:00',
            'status' => 'accepted',
            'total_amount' => 12.00,
            'cutoff_time' => now()->addDay()->setTime(18, 0),
        ]);
        OrderItem::create([
            'order_id' => $activeOrder->id,
            'product_id' => $products[8]->id,
            'product_name' => $products[8]->name,
            'unit_price' => $products[8]->price,
            'quantity' => 2,
            'subtotal' => $products[8]->price * 2,
        ]);

        $reviews = [
            [$products[0], $farmer, 5, 'Best tomatoes I have tasted this season!'],
            [$products[1], $farmer, 4, 'Fresh spinach, great for salads.'],
            [$products[8], $farmer2, 5, 'Eggs were perfect — rich yolks.'],
            [$products[9], $farmer2, 5, 'Honey is incredible on toast.'],
            [$products[13], $farmer3, 4, 'Crusty sourdough, well baked.'],
            [$products[11], $farmer3, 5, 'Basil smelled amazing.'],
            [$products[3], $farmer, 4, 'Crisp apples, kids loved them.'],
            [$products[7], $farmer2, 3, 'Good cheddar but a bit pricey.'],
        ];

        foreach ($reviews as [$product, $fp, $rating, $comment]) {
            Review::create([
                'customer_id' => $customer->id,
                'farmer_id' => $fp->id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'rating' => $rating,
                'comment' => $comment,
                'status' => 'approved',
                'farmer_reply' => $rating >= 5 ? 'Thank you for supporting our stall!' : null,
            ]);
        }

        // —— Announcements ——
        Announcement::create([
            'title' => 'Saturday peak hours',
            'message' => 'Expect busy pickup windows between 10–12. Arrive in your booked slot.',
            'published_by' => $admin->id,
            'published_at' => now()->subDay(),
            'status' => 'published',
            'priority' => 'medium',
        ]);
        Announcement::create([
            'title' => 'New farmers onboarded',
            'message' => 'Welcome Herb Haven and Sunrise Dairy to MarketLink this week.',
            'published_by' => $admin->id,
            'published_at' => now()->subDays(2),
            'status' => 'published',
            'priority' => 'low',
        ]);
        Announcement::create([
            'title' => 'Holiday market hours',
            'message' => 'Some markets close early on holidays — check each market page.',
            'published_by' => $admin->id,
            'published_at' => now(),
            'expires_at' => now()->addMonth(),
            'status' => 'published',
            'priority' => 'high',
        ]);

        // —— Chatbot FAQs ——
        $faqs = [
            ['How do I place a pre-order?', 'Browse products, add items to your cart, then checkout with a pickup date and time slot. Pay in person at the stall.', 'orders', ['pre-order', 'order', 'checkout', 'cart']],
            ['Do you deliver?', 'No. MarketLink is pickup-only at the farmers market stall. There is no delivery.', 'pickup', ['deliver', 'delivery', 'shipping']],
            ['How do I pay?', 'There is no online payment. Bring cash or card as accepted by the farmer and pay when you pick up.', 'payment', ['pay', 'payment', 'cash', 'card']],
            ['Can I cancel my order?', 'Yes, you can modify or cancel before the order cutoff time shown on your order page.', 'orders', ['cancel', 'modify', 'change', 'cutoff']],
            ['How do farmers get approved?', 'Farmers register with stall details. An admin reviews and approves before products appear publicly.', 'farmers', ['approve', 'farmer', 'register', 'approval']],
            ['Where is my pickup location?', 'Each order lists the market and farmer stall. Use the market map for directions via OpenStreetMap.', 'pickup', ['pickup', 'location', 'stall', 'map']],
        ];
        foreach ($faqs as [$q, $a, $cat, $kw]) {
            ChatbotFaq::create(['question' => $q, 'answer' => $a, 'category' => $cat, 'keywords' => $kw]);
        }

        Setting::setValue('site_tagline', 'Fresh from local farmers markets');
        Setting::setValue('support_email', 'support@marketlink.com');
        Setting::setValue('default_cutoff_hours', '24');
        Setting::setValue('low_stock_threshold', '5');

        AppNotification::notify(
            $customer,
            'welcome',
            'Welcome to MarketLink',
            'Browse nearby markets and place your first pre-order for pickup.',
            []
        );
        AppNotification::notify(
            $farmerUser,
            'welcome',
            'Stall ready',
            'Your farmer account is approved. Start managing stock and orders.',
            []
        );
    }
}
