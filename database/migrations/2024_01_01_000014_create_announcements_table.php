<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('message');
            $table->unsignedBigInteger('published_by');
            $table->dateTime('published_at')->nullable()->index();
            $table->dateTime('expires_at')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();

            $table->foreign('published_by', 'announcements_publisher_fk')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
