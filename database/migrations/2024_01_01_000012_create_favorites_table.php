<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('farmer_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('market_id')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('farmer_id');
            $table->index('product_id');
            $table->index('market_id');
            $table->unique(['customer_id', 'farmer_id', 'product_id', 'market_id'], 'favorites_unique');
            $table->foreign('customer_id', 'favorites_customer_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('farmer_id', 'favorites_farmer_fk')->references('id')->on('farmer_profiles')->cascadeOnDelete();
            $table->foreign('product_id', 'favorites_product_fk')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('market_id', 'favorites_market_fk')->references('id')->on('markets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
