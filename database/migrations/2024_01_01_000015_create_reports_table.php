<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 100)->index();
            $table->unsignedBigInteger('generated_by');
            $table->json('data');
            $table->dateTime('generated_at')->index();
            $table->timestamps();

            $table->foreign('generated_by', 'reports_generator_fk')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
