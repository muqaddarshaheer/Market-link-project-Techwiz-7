<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['cart_id', 'product_id']);
            $table->index('cart_id');
            $table->index('product_id');
            $table->foreign('cart_id', 'cart_items_cart_fk')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('product_id', 'cart_items_product_fk')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
