<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmer_profiles')->cascadeOnDelete();
            $table->string('name', 120);
            $table->decimal('area_amount', 10, 2)->nullable();
            $table->string('area_unit', 30)->default('kanal');
            $table->string('crop_name', 120)->nullable();
            $table->string('crop_key', 60)->nullable();
            $table->date('planted_on')->nullable();
            $table->date('expected_harvest')->nullable();
            $table->string('stage', 30)->default('empty');
            $table->string('soil_type', 60)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['farmer_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_lands');
    }
};
