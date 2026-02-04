<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@serviq.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@serviq.com',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
        }

        if (!User::where('email', 'user@serviq.com')->exists()) {
            User::create([
                'name' => 'Test User',
                'email' => 'user@serviq.com',
                'password' => Hash::make('user123'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]);
        }
    }
}
