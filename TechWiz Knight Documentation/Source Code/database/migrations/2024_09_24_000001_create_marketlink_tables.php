<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('stall_name', 100);
            $table->text('business_description')->nullable();
            $table->string('contact_person', 100);
            $table->json('operating_days')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->string('logo')->nullable();
            $table->json('pickup_slots')->nullable();
            $table->unsignedSmallInteger('cutoff_hours')->default(12);
            $table->json('weekly_stock_template')->nullable();
            $table->timestamps();
        });

        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->index();
            $table->text('address');
            $table->string('city', 100)->index();
            $table->json('operating_days')->nullable();
            $table->time('opening_time');
            $table->time('closing_time');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('map_provider', 50)->default('OpenStreetMap');
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('farmer_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmer_profiles')->cascadeOnDelete();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('stall_number', 50)->nullable();
            $table->string('operating_day', 50)->nullable();
            $table->text('pickup_notes')->nullable();
            $table->timestamps();
            $table->unique(['farmer_id', 'market_id']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmer_profiles')->cascadeOnDelete();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 150)->index();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->index();
            $table->enum('unit', ['kg', 'gram', 'dozen', 'bunch', 'litre', 'piece', 'pack']);
            $table->unsignedInteger('stock_quantity')->default(0)->index();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true)->index();
            $table->boolean('is_sold_out')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['cart_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('farmer_profiles')->cascadeOnDelete();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->date('pickup_date')->index();
            $table->string('pickup_slot', 100)->nullable();
            $table->enum('status', ['placed', 'accepted', 'declined', 'ready_for_pickup', 'completed', 'cancelled'])->default('placed')->index();
            $table->decimal('total_amount', 10, 2);
            $table->text('customer_note')->nullable();
            $table->dateTime('cutoff_time')->nullable();
            $table->text('farmer_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name', 150);
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->nullable()->constrained('farmer_profiles')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->index();
            $table->text('comment')->nullable();
            $table->text('farmer_reply')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('farmer_id')->nullable()->constrained('farmer_profiles')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('market_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->index(['customer_id', 'farmer_id']);
            $table->index(['customer_id', 'product_id']);
            $table->index(['customer_id', 'market_id']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100)->index();
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->foreignId('published_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('published_at')->nullable()->index();
            $table->dateTime('expires_at')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 100)->index();
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->json('data');
            $table->dateTime('generated_at')->index();
        });

        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question')->index();
            $table->text('answer');
            $table->string('category', 100)->nullable();
            $table->json('keywords')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('chatbot_faqs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('farmer_markets');
        Schema::dropIfExists('markets');
        Schema::dropIfExists('farmer_profiles');
    }
};
