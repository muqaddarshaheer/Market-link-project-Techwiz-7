<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('market_id');
            $table->date('pickup_date')->index();
            $table->string('pickup_slot', 100)->nullable();
            $table->enum('status', [
                'placed', 'accepted', 'declined', 'ready_for_pickup', 'completed', 'cancelled',
            ])->default('placed')->index();
            $table->decimal('total_amount', 10, 2);
            $table->text('customer_note')->nullable();
            $table->dateTime('cutoff_time')->nullable();
            $table->text('farmer_notes')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('farmer_id');
            $table->index('market_id');
            $table->foreign('customer_id', 'orders_customer_fk')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('farmer_id', 'orders_farmer_fk')->references('id')->on('farmer_profiles')->cascadeOnDelete();
            $table->foreign('market_id', 'orders_market_fk')->references('id')->on('markets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
