<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'admin' => 'Admin@123',
            'farmer' => 'Farmer@123',
            'customer' => 'Customer@123',
        ];

        foreach ($map as $role => $password) {
            DB::table('users')->where('role', $role)->update([
                'password' => Hash::make($password),
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
