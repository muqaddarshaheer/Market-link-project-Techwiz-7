<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_messages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE order_messages MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('order_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('order_messages', 'guest_name')) {
                $table->string('guest_name', 100)->nullable()->after('user_id');
            }
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_messages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            if (Schema::hasColumn('order_messages', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
        });

        DB::statement('ALTER TABLE order_messages MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('order_messages', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
