<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DiscountCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 2. تعطيل فحص المفاتيح الأجنبية
        Schema::disableForeignKeyConstraints();

        // الآن يمكننا تفريغ الجدول بأمان
        DB::table('discount_codes')->truncate();

        // 3. إعادة تفعيل فحص المفاتيح الأجنبية بعد الانتهاء
        Schema::enableForeignKeyConstraints();

        $coupons = [];
        $now = Carbon::now();

        // حلقة لإنشاء 50 كوبونًا
        for ($i = 0; $i < 50; $i++) {
            
            $maxUses = rand(50, 200);
            $currentUses = rand(0, $maxUses);
            
            if ($currentUses >= $maxUses) {
                $isActive = false;
            } else {
                $isActive = (rand(1, 100) <= 85);
            }

            $coupons[] = [
                'code' => strtoupper(Str::random(8)),
                'discount_rate' => rand(5, 50) / 100,
                'is_active' => $isActive,
                'max_uses' => $maxUses,
                'current_uses' => $currentUses,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('discount_codes')->insert($coupons);
    }
}

