<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_markets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('market_id');
            $table->string('stall_number', 50)->nullable();
            $table->string('operating_day', 50)->nullable();
            $table->text('pickup_notes')->nullable();
            $table->timestamps();

            $table->unique(['farmer_id', 'market_id']);
            $table->index('farmer_id');
            $table->index('market_id');
            $table->foreign('farmer_id', 'fm_farmer_fk')->references('id')->on('farmer_profiles')->cascadeOnDelete();
            $table->foreign('market_id', 'fm_market_fk')->references('id')->on('markets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_markets');
    }
};
