<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('quality', ['premium', 'fresh', 'standard'])->default('fresh')->after('unit');
        });

        DB::statement('ALTER TABLE orders MODIFY customer_id BIGINT UNSIGNED NULL');

        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_name', 100)->nullable()->after('customer_id');
            $table->string('guest_phone', 20)->nullable()->after('guest_name');
            $table->string('guest_address', 255)->nullable()->after('guest_phone');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('total_amount');
        });

        $pins = ['admin' => '0000', 'farmer' => '1111', 'customer' => '2222'];
        foreach ($pins as $role => $pin) {
            DB::table('users')->where('role', $role)->update(['password' => Hash::make($pin)]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('quality');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_address', 'payment_status']);
        });
    }
};
