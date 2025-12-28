<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use App\Repositories\AppSettingRepository;

class PriceCalculationService{
/**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        return round($distance, 1);
    }
    public function calculateBasePriceBasedOnVehicleType($distanceKm, $vehicle){
        
    }

}