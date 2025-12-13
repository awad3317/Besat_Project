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
                'type' => 'دباب',
                'description' =>'دباب مريح وسهل الركوب',
                'max_passengers' => 2,
                'min_price'=>200
            ],
            [
                'type' => 'فوكسي',
                'description' => 'سيارة واسعة مناسبة للعائلات والطرق الوعرة',
                'max_passengers' => 7,
                 'min_price'=>200
            ],
            [
                'type' => ' تاكسي ',
                'description' => 'مركبة كبيرة مناسبة لنقل البضائع والمجموعات',
                'max_passengers' => 6,
                 'min_price'=>200
            ],
           
        ];
        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

    }
}
