<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriversSeeder extends Seeder
{
    /**
     * تشغيل Seeder
     */
    public function run(): void
    {
        
        // بيانات السائقين
        $drivers = [
            [
                'vehicle_id' => 1,
                'name' => 'أحمد محمد',
                'phone' => '967123456789',
                'whatsapp_number' => '967123456789',
                'city' => 'صنعاء',
                'plate_number' => 'ص ن 1234',
                'latitude' => 15.3694,
                'longitude' => 44.1910,
                'is_active' => true,
                'is_online' => true,
                'device_token' => 'device_token_001',
            ],
            [
                'vehicle_id' => 1,
                'name' => 'سعيد علي',
                'phone' => '967987654321',
                'whatsapp_number' => '967987654321',
                'city' => 'عدن',
                'plate_number' => 'ع د 5678',
                'latitude' => 12.7855,
                'longitude' => 45.0187,
                'is_active' => true,
                'is_online' => false,
                'device_token' => 'device_token_002',
            ],
            [
                'vehicle_id' => 1,
                'name' => 'خالد حسين',
                'phone' => '967555444333',
                'whatsapp_number' => '967555444333',
                'city' => 'تعز',
                'plate_number' => 'ت ز 9012',
                'latitude' => 13.5789,
                'longitude' => 44.0193,
                'is_active' => true,
                'is_online' => true,
                'device_token' => 'device_token_003',
            ],
            [
                'vehicle_id' => 1,
                'name' => 'محمد عبدالله',
                'phone' => '967222333444',
                'whatsapp_number' => '967222333444',
                'city' => 'حضرموت',
                'plate_number' => 'ح ض 3456',
                'latitude' => 14.5567,
                'longitude' => 49.1245,
                'is_active' => true,
                'is_online' => true,
                'device_token' => 'device_token_004',
            ],
            [
                'vehicle_id' => 1,
                'name' => 'عبدالرحمن سالم',
                'phone' => '967666777888',
                'whatsapp_number' => '967666777888',
                'city' => 'المكلا',
                'plate_number' => 'م ك 7890',
                'latitude' => 14.5302,
                'longitude' => 49.1315,
                'is_active' => false, // سائق غير نشط
                'is_online' => false,
                'device_token' => 'device_token_005',
            ],
        ];

        // إدخال السائقين في قاعدة البيانات
        foreach ($drivers as $driverData) {
            Driver::updateOrCreate(
                ['phone' => $driverData['phone']], // البحث باستخدام رقم الهاتف
                $driverData
            );
        }

    }
}