<?php

namespace App\Services;

use App\Models\Driver;

class DriverLocationService{
    public function getNearestDrivers($latitude, $longitude, $limit, $radiusKm)
    {
        return Driver::select('drivers.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->where('is_active', true)
            ->where('is_online', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc')
            ->limit($limit)
            ->get();
    }


}