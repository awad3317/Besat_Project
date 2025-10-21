<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'name' => 'سيارة سيدان',
                'type' => 'sedan',
                'description' => 'سيارة مريحة مناسبة للرحلات الفردية والعائلية الصغيرة',
                'max_passengers' => 4
            ],
            [
                'name' => 'سيارة دفع رباعي',
                'type' => 'suv',
                'description' => 'سيارة واسعة مناسبة للعائلات والطرق الوعرة',
                'max_passengers' => 7
            ],
            [
                'name' => 'فان نقل',
                'type' => 'van',
                'description' => 'مركبة كبيرة مناسبة لنقل البضائع والمجموعات',
                'max_passengers' => 12
            ],
            [
                'name' => 'سيارة اقتصادية',
                'type' => 'economy',
                'description' => 'سيارة موفرة للوقود مناسبة للرحلات القصيرة',
                'max_passengers' => 4
            ],
            [
                'name' => 'سيارة فاخرة',
                'type' => 'luxury',
                'description' => 'سيارة فاخرة مع وسائل راحة متقدمة',
                'max_passengers' => 4
            ],
            [
                'name' => 'دراجة نارية',
                'type' => 'motorcycle',
                'description' => 'مركبة سريعة ومناسبة للطرق المزدحمة',
                'max_passengers' => 2
            ],
            [
                'name' => 'شاحنة صغيرة',
                'type' => 'truck',
                'description' => 'مناسبة لنقل الأثاث والبضائع المتوسطة',
                'max_passengers' => 3
            ],
            [
                'name' => 'حافلة صغيرة',
                'type' => 'minibus',
                'description' => 'مناسبة للمجموعات الكبيرة والرحلات الجماعية',
                'max_passengers' => 20
            ]
        ];
        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

    }
}
