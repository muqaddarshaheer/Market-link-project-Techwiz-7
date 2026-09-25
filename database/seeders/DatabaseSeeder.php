<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\ChatbotFaq;
use App\Models\FarmerProfile;
use App\Models\Favorite;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'Avery Admin',
            'email' => 'admin@marketlink.com',
            'phone' => '555-0100',
            'address' => '1 Market Square',
            'password' => '0000',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $customer = User::query()->create([
            'name' => 'Casey Customer',
            'email' => 'customer@marketlink.com',
            'phone' => '555-0142',
            'address' => '88 Orchard Street',
            'password' => '2222',
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $extraCustomers = collect([
            ['Nora Blake', 'nora@marketlink.com'],
            ['Jonah Reed', 'jonah@marketlink.com'],
        ])->map(fn ($row) => User::query()->create([
            'name' => $row[0],
            'email' => $row[1],
            'phone' => '555-0199',
            'address' => '14 Elm Court',
            'password' => '2222',
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]));

        $days = [
            ['Saturday', 'Sunday'],
            ['Wednesday', 'Saturday'],
            ['Friday', 'Saturday'],
        ];

        $farmerUsers = [
            ['Harper Green', 'farmer@marketlink.com', 'Green Row Farm', 'approved', 'active', 'images/farmers/farmer-harper.jpg', '0322066091'],
            ['Miles Ortega', 'miles@marketlink.com', 'Ortega Honey', 'approved', 'active', 'images/farmers/farmer-miles.jpg', '03401227617'],
            ['Priya Shah', 'priya@marketlink.com', 'Shah Herbs', 'approved', 'active', 'images/farmers/male-farmer.jpg', '03001234567'],
        ];

        $farmers = [];
        foreach ($farmerUsers as $i => $row) {
            $user = User::query()->create([
                'name' => $row[0],
                'email' => $row[1],
                'phone' => $row[6],
                'address' => (20 + $i).' Farm Road',
                'password' => '1111',
                'role' => 'farmer',
                'status' => $row[4],
                'email_verified_at' => now(),
            ]);
            $farmers[] = FarmerProfile::query()->create([
                'user_id' => $user->id,
                'stall_name' => $row[2],
                'business_description' => $row[2].' grows for weekend markets and packs pre-orders the morning of pickup.',
                'contact_person' => $row[0],
                'operating_days' => $days[$i],
                'address' => $user->address,
                'latitude' => 30.2672 + ($i * 0.01),
                'longitude' => -97.7431 + ($i * 0.01),
                'approval_status' => $row[3],
                'logo' => $row[5],
                'pickup_slots' => [['label' => '08:00-10:00'], ['label' => '10:00-12:00']],
                'cutoff_hours' => 12,
            ]);
        }

        $markets = [
            ['Riverside Saturday Market', '100 River Walk', 'Austin', ['Saturday'], 30.2640, -97.7460],
            ['Eastside Sunset Market', '420 Manor Road', 'Austin', ['Wednesday', 'Saturday'], 30.2840, -97.7200],
            ['Oak Hill Growers', '15 Oak Hill Blvd', 'Austin', ['Sunday'], 30.2400, -97.7700],
            ['Downtown Lunch Market', '9 Congress Ave', 'Austin', ['Friday'], 30.2678, -97.7428],
        ];

        $marketModels = [];
        foreach ($markets as $row) {
            $marketModels[] = Market::query()->create([
                'name' => $row[0],
                'address' => $row[1],
                'city' => $row[2],
                'operating_days' => $row[3],
                'opening_time' => '08:00',
                'closing_time' => '13:00',
                'latitude' => $row[4],
                'longitude' => $row[5],
                'status' => 'active',
            ]);
        }

        $farmers[0]->markets()->sync([
            $marketModels[0]->id => ['stall_number' => 'A12'],
            $marketModels[1]->id => ['stall_number' => 'B4'],
        ]);
        $farmers[1]->markets()->sync([
            $marketModels[0]->id => ['stall_number' => 'C2'],
            $marketModels[3]->id => ['stall_number' => 'D1'],
        ]);
        $farmers[2]->markets()->sync([
            $marketModels[2]->id => ['stall_number' => 'E8'],
        ]);

        $categories = [
            ['Vegetables', 'bi-basket2', 'Seasonal vegetables'],
            ['Fruits', 'bi-apple', 'Orchard fruit'],
            ['Dairy', 'bi-cup-straw', 'Milk and cheese'],
            ['Baked Goods', 'bi-bread-slice', 'Breads and pastries'],
            ['Herbs', 'bi-flower1', 'Culinary herbs'],
            ['Eggs', 'bi-egg', 'Farm eggs'],
            ['Honey', 'bi-droplet', 'Raw honey'],
        ];
        $categoryModels = [];
        foreach ($categories as $row) {
            $categoryModels[$row[0]] = Category::query()->create([
                'name' => $row[0],
                'icon' => $row[1],
                'description' => $row[2],
            ]);
        }

        $catalog = [
            ['Cherry tomatoes', 'Vegetables', 0, 0, 4.50, 'kg', 24, true],
            ['Rainbow chard', 'Vegetables', 0, 1, 3.25, 'bunch', 18, true],
            ['New potatoes', 'Vegetables', 0, 0, 2.80, 'kg', 30, false],
            ['Sweet corn', 'Vegetables', 0, 1, 1.25, 'piece', 40, true],
            ['Peaches', 'Fruits', 0, 0, 5.00, 'kg', 16, true],
            ['Blueberries', 'Fruits', 0, 1, 6.50, 'pack', 12, true],
            ['Goat cheese', 'Dairy', 0, 0, 8.00, 'piece', 10, false],
            ['Whole milk', 'Dairy', 0, 1, 3.75, 'litre', 14, false],
            ['Sourdough loaf', 'Baked Goods', 1, 0, 7.00, 'piece', 20, true],
            ['Cinnamon rolls', 'Baked Goods', 1, 3, 9.50, 'pack', 8, true],
            ['Basil bunch', 'Herbs', 2, 2, 2.50, 'bunch', 15, false],
            ['Mint bunch', 'Herbs', 2, 2, 2.25, 'bunch', 15, false],
            ['Rosemary', 'Herbs', 0, 0, 2.00, 'bunch', 11, false],
            ['Dozen eggs', 'Eggs', 0, 0, 6.00, 'dozen', 22, true],
            ['Duck eggs', 'Eggs', 0, 1, 8.50, 'dozen', 6, false],
            ['Wildflower honey', 'Honey', 1, 0, 12.00, 'piece', 9, true],
            ['Clover honey', 'Honey', 1, 3, 10.00, 'piece', 11, false],
            ['Mixed salad greens', 'Vegetables', 0, 0, 4.00, 'pack', 19, true],
        ];

        $products = [];
        foreach ($catalog as $i => $row) {
            $farmer = $farmers[$row[2]];
            if ($farmer->approval_status !== 'approved') {
                continue;
            }
            $products[] = Product::query()->create([
                'farmer_id' => $farmer->id,
                'market_id' => $marketModels[$row[3]]->id,
                'category_id' => $categoryModels[$row[1]]->id,
                'name' => $row[0],
                'description' => 'Picked for this week’s market. Pre-order and pay at the stall.',
                'price' => $row[4],
                'unit' => $row[5],
                'stock_quantity' => $row[6],
                'is_available' => true,
                'is_sold_out' => false,
                'is_featured' => $row[7],
                'views_count' => 10 + $i,
            ]);
        }

        $order = Order::query()->create([
            'order_number' => 'ML-DEMO-1001',
            'customer_id' => $customer->id,
            'farmer_id' => $farmers[0]->id,
            'market_id' => $marketModels[0]->id,
            'pickup_date' => now()->subDays(3)->toDateString(),
            'pickup_slot' => '08:00-10:00',
            'status' => 'completed',
            'total_amount' => 10.50,
            'customer_note' => 'Please pack the tomatoes ripe.',
            'cutoff_time' => now()->subDays(4),
        ]);
        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $products[0]->id,
            'product_name' => $products[0]->name,
            'unit_price' => $products[0]->price,
            'quantity' => 1,
            'subtotal' => $products[0]->price,
        ]);
        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $products[4]->id,
            'product_name' => $products[4]->name,
            'unit_price' => $products[4]->price,
            'quantity' => 1,
            'subtotal' => $products[4]->price,
        ]);
        $order->update(['total_amount' => $products[0]->price + $products[4]->price]);

        $open = Order::query()->create([
            'order_number' => 'ML-DEMO-1002',
            'customer_id' => $customer->id,
            'farmer_id' => $farmers[1]->id,
            'market_id' => $marketModels[0]->id,
            'pickup_date' => now()->addDays(2)->toDateString(),
            'pickup_slot' => '10:00-12:00',
            'status' => 'placed',
            'total_amount' => $products[8]->price,
            'cutoff_time' => now()->addDay(),
        ]);
        OrderItem::query()->create([
            'order_id' => $open->id,
            'product_id' => $products[8]->id,
            'product_name' => $products[8]->name,
            'unit_price' => $products[8]->price,
            'quantity' => 1,
            'subtotal' => $products[8]->price,
        ]);

        $comments = [
            [5, 'The tomatoes tasted like August. Easy pickup.'],
            [4, 'Peaches were sweet and packed carefully.'],
            [5, 'Best sourdough at the market.'],
            [4, 'Honey crystallized a little, still delicious.'],
            [5, 'Eggs were fresh and the stall was organized.'],
            [3, 'Good greens, a few leaves were tired.'],
        ];
        foreach ($comments as $i => $comment) {
            $product = $products[$i % count($products)];
            Review::query()->create([
                'customer_id' => $i % 2 === 0 ? $customer->id : $extraCustomers[0]->id,
                'farmer_id' => $product->farmer_id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'rating' => $comment[0],
                'comment' => $comment[1],
                'status' => 'approved',
                'helpful_count' => $i,
            ]);
        }

        Favorite::query()->create(['customer_id' => $customer->id, 'product_id' => $products[0]->id]);
        Favorite::query()->create(['customer_id' => $customer->id, 'farmer_id' => $farmers[0]->id]);
        Favorite::query()->create(['customer_id' => $customer->id, 'market_id' => $marketModels[0]->id]);

        Announcement::query()->create([
            'title' => 'Saturday markets open at 8',
            'message' => 'Pre-orders close 12 hours before your pickup slot. Pay at the stall.',
            'published_by' => $admin->id,
            'published_at' => now()->subDay(),
            'status' => 'published',
            'priority' => 'high',
        ]);
        Announcement::query()->create([
            'title' => 'New honey stall',
            'message' => 'Ortega Honey is approved and taking weekend pre-orders.',
            'published_by' => $admin->id,
            'published_at' => now(),
            'status' => 'published',
            'priority' => 'medium',
        ]);

        $faqs = [
            ['What are market hours?', 'Most markets run 8:00 to 13:00 on their listed days. Open a market page for the exact schedule.', 'hours', 'hours,time,open,timing'],
            ['How do I pre-order?', 'Add products to your cart, choose a pickup date and slot, and place the order before cutoff. You pay the farmer in person.', 'orders', 'order,pre-order,cart,checkout'],
            ['When is pickup?', 'Pickup is during the slot you chose, at the market attached to each product.', 'pickup', 'pickup,collect,slot,window'],
            ['Can I cancel?', 'You can change or cancel a pre-order until the cutoff time shown on the order.', 'orders', 'cancel,change,cutoff'],
            ['Do you deliver?', 'No. MarketLink is pickup only. There is no courier and no online payment.', 'pickup', 'delivery,pay,payment'],
        ];
        foreach ($faqs as $faq) {
            ChatbotFaq::query()->create([
                'question' => $faq[0],
                'answer' => $faq[1],
                'category' => $faq[2],
                'keywords' => explode(',', $faq[3]),
            ]);
        }

        Setting::putMany([
            'platform_name' => 'MarketLink',
            'tagline' => 'Local farms, ready for pickup',
            'contact_email' => 'hello@marketlink.test',
            'contact_phone' => '555-0100',
            'contact_address' => '100 River Walk, Austin',
            'facebook' => 'https://facebook.com',
            'instagram' => 'https://instagram.com',
        ]);

        $notify = app(NotificationService::class);
        $notify->send($customer, 'order_placed', 'Pre-order placed', 'Order ML-DEMO-1002 is waiting for Ortega Honey.', ['order_id' => $open->id]);
        $notify->send($farmers[1]->user, 'new_order', 'New pre-order', 'Casey Customer placed ML-DEMO-1002.', ['order_id' => $open->id]);

        DB::table('reports')->insert([
            'report_type' => 'seed_snapshot',
            'generated_by' => $admin->id,
            'data' => json_encode(['note' => 'Demo snapshot']),
            'generated_at' => now(),
        ]);
    }
}
