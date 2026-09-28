<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmer_profiles')->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('category', 40)->default('other');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('crop_name', 120)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            $table->index(['farmer_id', 'expense_date']);
            $table->index(['farmer_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_expenses');
    }
};
