<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('farmer_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedTinyInteger('rating')->index();
            $table->text('comment')->nullable();
            $table->text('farmer_reply')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->index('customer_id');
            $table->index('farmer_id');
            $table->index('product_id');
            $table->index('order_id');
            $table->foreign('customer_id', 'reviews_customer_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('farmer_id', 'reviews_farmer_fk')->references('id')->on('farmer_profiles')->nullOnDelete();
            $table->foreign('product_id', 'reviews_product_fk')->references('id')->on('products')->nullOnDelete();
            $table->foreign('order_id', 'reviews_order_fk')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
