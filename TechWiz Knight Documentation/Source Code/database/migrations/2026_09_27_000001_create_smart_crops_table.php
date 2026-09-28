<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_crops', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name_en', 120);
            $table->string('name_ur', 120);
            $table->string('emoji', 16)->nullable();
            $table->string('category', 32)->default('vegetable');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->decimal('temp_min_c', 5, 1)->nullable();
            $table->decimal('temp_max_c', 5, 1)->nullable();
            $table->unsignedTinyInteger('ideal_humidity_min')->nullable();
            $table->unsignedTinyInteger('ideal_humidity_max')->nullable();
            $table->boolean('sensitive_to_rain')->default(true);
            $table->json('details_en');
            $table->json('details_ur');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_crops');
    }
};
