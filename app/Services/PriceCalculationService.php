<?php

namespace App\Services;

use App\Models\vehicle_pricing;
use App\Repositories\VehicleRepository;
use App\Repositories\AppSettingRepository;


class PriceCalculationService{
/**
     * Create a new class instance.
     */
    public function __construct(private VehicleRepository $vehicleRepository)
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
        
        if (empty($vehicle->pricing)) {
            return false;
        }
        $price_per_km = 0;

        foreach ($vehicle->pricing as $pricing) {
            if ($distanceKm >= $pricing->min_distance_km && $distanceKm <= $pricing->max_distance_km) {
                $price_per_km = $pricing->base_price;
                break; 
            }
        }
        // if ($price_per_km == 11){
        //     $last_tier = $vehicle->pricing->count($vehicle->pricing-1);
        //     $price_per_km = $last_tier->price_per_km;
        // }
        if ($price_per_km == 0) {
            $price_per_km=$vehicle->min_price;
        }
        $total_price = $distanceKm * $price_per_km;
        return max($total_price, $vehicle->min_price);
        // return $total_price;
    
    }
    
}