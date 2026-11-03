<?php

namespace App\Services;

use App\Models\Driver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DriverLocationService
{
    /**
     * Get nearest drivers using expanding-radius search (for push notifications / auto-dispatch).
     */
    public function getNearestDrivers($latitude, $longitude, $limit = 8, $maxRadiusKm = 50)
    {
        $foundDrivers = collect();
        $currentRadius = 2;

        while ($currentRadius <= $maxRadiusKm && $foundDrivers->count() < $limit) {
            $remaining = $limit - $foundDrivers->count();

            $driversInRadius = Driver::select('drivers.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$latitude, $longitude, $latitude]
                )
                ->where('is_active', true)
                ->where('is_online', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereNotNull('device_token')
                ->whereNotIn('id', $foundDrivers->pluck('id')->toArray())
                ->having('distance', '<=', $currentRadius)
                ->orderBy('distance', 'asc')
                ->limit($remaining * 2)
                ->get();

            foreach ($driversInRadius as $driver) {
                $foundDrivers->push($driver);
                if ($foundDrivers->count() >= $limit) {
                    break;
                }
            }

            if ($foundDrivers->count() == 0) {
                $currentRadius += 5;
            } else {
                $currentRadius += 3;
            }
        }

        if ($foundDrivers->isEmpty()) {
            return null;
        }

        return $foundDrivers;
    }

    /**
     * Get nearest drivers for the admin dashboard dispatch modal.
     *
     * Uses a safe Haversine formula with LEAST/GREATEST to prevent acos() domain errors.
     * Drivers with valid coordinates are sorted by distance ASC.
     * Drivers with NULL coordinates are appended at the bottom.
     *
     * @param float      $latitude       Pickup latitude
     * @param float      $longitude      Pickup longitude
     * @param string     $search         Optional name/phone search term
     * @param bool       $onlineOnly     Filter to only online drivers
     * @param int|null   $vehicleId      Filter to only drivers with this vehicle_id
     * @param int        $limit          Maximum number of results
     * @return Collection
     */
    public function getNearestDriversForDashboard(
        float $latitude,
        float $longitude,
        string $search = '',
        bool $onlineOnly = false,
        ?int $vehicleId = null,
        int $limit = 30
    ): Collection {
        // Safe Haversine: clamp the inner value to [-1, 1] to prevent SQL acos domain errors
        $haversine = <<<SQL
            CASE
                WHEN drivers.latitude IS NOT NULL AND drivers.longitude IS NOT NULL THEN
                    6371 * acos(
                        LEAST(1.0, GREATEST(-1.0,
                            cos(radians(?)) * cos(radians(drivers.latitude))
                            * cos(radians(drivers.longitude) - radians(?))
                            + sin(radians(?)) * sin(radians(drivers.latitude))
                        ))
                    )
                ELSE NULL
            END
        SQL;

        return Driver::select('drivers.*')
            ->selectRaw("{$haversine} AS distance_km", [$latitude, $longitude, $latitude])
            ->with('vehicle:id,type')
            ->where('is_banned', false)
            ->where('is_active', true)
            ->when($onlineOnly, fn ($q) => $q->where('is_online', true))
            ->when($vehicleId, fn ($q) => $q->where('vehicle_id', $vehicleId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%");
                });
            })
            // Drivers with coordinates first (sorted by distance), then NULLs at bottom
            ->orderByRaw('CASE WHEN drivers.latitude IS NULL OR drivers.longitude IS NULL THEN 1 ELSE 0 END ASC')
            ->orderByRaw("{$haversine} ASC", [$latitude, $longitude, $latitude])
            ->limit($limit)
            ->get();
    }
}
