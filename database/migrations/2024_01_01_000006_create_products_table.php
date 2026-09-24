<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('market_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name', 150)->index();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->index();
            $table->enum('unit', ['kg', 'gram', 'dozen', 'bunch', 'litre', 'piece', 'pack'])->default('kg');
            $table->unsignedInteger('stock_quantity')->default(0)->index();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true)->index();
            $table->boolean('is_sold_out')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('weekly_stock_template')->nullable();
            $table->timestamps();

            $table->index('farmer_id');
            $table->index('market_id');
            $table->index('category_id');
            $table->foreign('farmer_id', 'products_farmer_fk')->references('id')->on('farmer_profiles')->cascadeOnDelete();
            $table->foreign('market_id', 'products_market_fk')->references('id')->on('markets')->cascadeOnDelete();
            $table->foreign('category_id', 'products_category_fk')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
