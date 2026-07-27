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
    
        $drivers = [
            [
                'vehicle_id' => 1,
                'name' => 'أحمد محمد',
                'phone' => '967123456789',
                'whatsapp_number' => '967123456789',
                'city' => 'صنعاء',
                'district' => 'السبعين', 
                'vehicle_image' => 'drivers/vehicles/default_car1.png',
                'driver_image' => 'drivers/avatars/default_driver1.png',  
                'identity_image' => 'drivers/identities/default_id1.png',
                'plate_number' => 'ص ن 1234',
                'identity_number' => '1012345678',
                'latitude' => 15.3694,
                'longitude' => 44.1910,
                'password' => Hash::make('123456'),
                'is_active' => true,
                'is_online' => true,
                'device_token' => 'device_token_001',
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