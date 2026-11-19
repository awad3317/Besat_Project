<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['type' => 'superAdmin'],
            [
                'name' => 'تيار - بساط',
                'phone' => '967776023837',
                'whatsapp_number' => '967776023837',
                'phone_verified_at' => now(),
                'password' => '967776023837-admin',
                'is_banned' => false,
            ]
        );
    }
}
