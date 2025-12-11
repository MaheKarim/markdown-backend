<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'mahekarim.cse@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123456'), // Note: In production, use a strong password
                'role' => 'admin',
            ]
        );
    }
}
