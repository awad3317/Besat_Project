<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearTestDataSeeder extends Seeder
{
    /**
     * تفريغ بيانات التيست مع الإبقاء على:
     * - المستخدمين والمسؤولين (users, user_devices, personal_access_tokens)
     * - السائقين (drivers)
     * - البنوك وإعداداتها (banks, payment_steps, payment_fields, currencies, bank_currency)
     * - المركبات وتسعيرتها (vehicles, vehicle_pricings)
     * - إعدادات النظام وأوقات الذروة والإعلانات (app_settings, surcharges, ads)
     */
    public function run(): void
    {
        $tablesToTruncate = [
            // 1. الرحلات العامة وملحقاتها والتقييمات
            'request_stops',
            'request_surcharge',
            'ratings',
            'requests',

            // 2. حجوزات الخدمات (الطلبات الخاصة) ونقاط التوقف
            'service_booking_stops',
            'service_bookings',
            'special_orders',

            // 3. المحادثات والرسائل
            'messages',
            'conversations',

            // 4. تصفيات حسابات السائقين
            'driver_settlements',

            // 5. حركات المحفظة ونقاط الولاء التجريبية
            'wallet_transactions',
            'loyalty_point_transactions',

            // 6. الكوبونات وسجل استخدامها
            'discount_code_user',
            'discount_codes',

            // 7. الإشعارات وسجل النشاطات والأماكن المفضلة وأكواد التحقق
            'notifications',
            'activity_logs',
            'favorite_places',
            'otps',
        ];

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tablesToTruncate as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->command?->info("✔ تم تفريغ الجدول: {$table}");
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->command?->info('✅ تم مسح بيانات التيست بنجاح مع الإبقاء على المستخدمين، السائقين، البنوك، المركبات، وإعدادات النظام.');
    }
}
