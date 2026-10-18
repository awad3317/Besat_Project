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
        // تفريغ الجدول وإعادة تصفير الـ ID ليبدأ من 1 (أفضل من delete)
        DB::table('app_settings')->truncate();

        DB::table('app_settings')->insert([
            // 1. السطر الأول: إعدادات تطبيق المستخدمين (id = 1)
            [
                'app_type'               => 'user',
                'commission_rate'        => 10.00,
                'auto_assign_to_drivers' => false,
                'version'                => '1.0.1',
                'otp_enabled'            => true,
                'android_update_url'     => 'https://www.google.com/',
                'ios_update_url'         => 'https://www.google.com/',
                'company_website'        => 'https://www.instagram.com/codak.dev?igsh=emtmaDkzOWNxcTlm',
                'company_whatsapp'       => '+967774712979',
                'company_email'          => 'info@besat.com',
                'ref_no'                 => '1007',
                'maintenance_mode'       => false,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
            // 2. السطر الثاني: إعدادات تطبيق السائقين (id = 2)
            [
                'app_type'               => 'driver',
                'commission_rate'        => 10.00,
                'auto_assign_to_drivers' => false,
                'version'                => '1.0.0',
                'otp_enabled'            => true,
                'android_update_url'     => 'https://www.google.com/',
                'ios_update_url'         => 'https://www.google.com/',
                'company_website'        => 'https://www.instagram.com/codak.dev?igsh=emtmaDkzOWNxcTlm',
                'company_whatsapp'       => '+967774712979',
                'company_email'          => 'info@besat.com',
                'ref_no'                 => '1008',
                'maintenance_mode'       => false,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
        ]);
    }
}