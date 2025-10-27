<?php

namespace App\Services;

use App\Repositories\AppSettingRepository;

class CommissionCalculationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private AppSettingRepository $appSettingRepository)
    {
        //
    }

    public function calculateCommission(float $finalPrice){
        $setting = $this->appSettingRepository->getSetting();

        $commissionRate = $setting ? $setting->commission_rate : 10;

        $commission = ($finalPrice * $commissionRate) / 100;
        return $commission;
     }
}
