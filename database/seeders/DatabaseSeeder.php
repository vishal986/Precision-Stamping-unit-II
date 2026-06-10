<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@precision.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@precision.com',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
