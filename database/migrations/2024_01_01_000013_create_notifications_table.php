<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 100)->index();
            $table->string('title', 255);
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->foreign('user_id', 'notifications_user_fk')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
