<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Surcharge;

class SurchargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Surcharge::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        Surcharge::create([
            'name'          => 'زيادة الطقس الممطر',
            'type'          => 'conditional',
            'condition_key' => 'is_raining',
            'amount'        => 2000.00,
            'is_active'     => true,
        ]);
        Surcharge::create([
            'name'      => 'زيادة آخر الليل',
            'type'      => 'time_based',
            'condition_key' => 'is_night',
            'time_from' => '00:00:00',
            'time_to'   => '06:00:00',
            'amount'    => 3000.00,
            'is_active' => true,
        ]);
    }
}
