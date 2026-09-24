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
            $table->unsignedBigInteger('user_id');
            $table->string('stall_name', 100);
            $table->text('business_description')->nullable();
            $table->string('contact_person', 100);
            $table->json('operating_days')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->string('logo')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->foreign('user_id', 'farmer_profiles_user_fk')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_profiles');
    }
};
