<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('app_settings')->delete();

        // إضافة الإعدادات الأساسية
        DB::table('app_settings')->insert([
            [
                'commission_rate' => 10, // نسبة العمولة 10%
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
