<?php

namespace App\Services;

use App\Models\vehicle_pricing;
use App\Models\Surcharge;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Repositories\VehicleRepository;
use App\Repositories\AppSettingRepository;


class PriceCalculationService{
/**
     * Create a new class instance.
     */
    public function __construct(private VehicleRepository $vehicleRepository,private AppSettingRepository $appSettingRepository)
    {
        //
    }


    public function getdistanceInKm(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        $apiKey = env('GOOGLE_MAP_KEY');
        if (!$apiKey) {
            Log::error('Google Maps API Key is not configured.');
            return null;
        }
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
        $response = Http::get($url, [
            'origins'      => "{$lat1},{$lon1}",
            'destinations' => "{$lat2},{$lon2}",
            'mode'         => 'driving',
            'key'          => $apiKey,
        ]);
        if ($response->failed()) {
            Log::error('Google Distance Matrix API request failed: ' . $response->body());
            return null;
        }
        $data = $response->json();
        if ($data['status'] == 'OK' && $data['rows'][0]['elements'][0]['status'] == 'OK') {

            $element = $data['rows'][0]['elements'][0];
        // return [
        //     'distance_km'   => $element['distance']['value'] / 1000, // المسافة بالكيلومتر
        //     'distance_text' => $element['distance']['text'],       // نص المسافة (e.g., "5.4 km")
        //     'duration_mins' => $element['duration']['value'] / 60,   // الوقت بالدقائق
        //     'duration_text' => $element['duration']['text'],       // نص الوقت (e.g., "12 mins")
        // ];
            return floatval($element['distance']['text']);
        }
        Log::warning('Google Distance Matrix API returned status: ' . $data['status']);
        return null;
    }
    public function getPricePerKmByDistanceAndVehicle($distanceKm, $vehicle): float
    {
        if ($vehicle->pricing->isEmpty()) {
            return $vehicle->min_price;
        }
        $price_per_km = $vehicle->pricing->last()->base_price;
        foreach ($vehicle->pricing as $pricing) {
            if ($distanceKm >= $pricing->min_distance_km && $distanceKm <= $pricing->max_distance_km) {
                $price_per_km = $pricing->base_price;
                break;
            }
        }
        return $price_per_km;
    }

    public function calculatePrice($distanceKm,$price_per_km,$min_price)
    {
        $total_price = $distanceKm * $price_per_km;
        return max($total_price, $min_price);
    }

    public function calculateCommission($orginal_price){
        $commission_rate=$this->appSettingRepository->getSetting()->commission_rate;
        $commission_amount = $orginal_price * ($commission_rate / 100);
        return round($commission_amount);
    }

    public function getApplicableSurcharges()
    {
        $activeSurcharges = Surcharge::where('is_active', true)->get();
        $applicableSurcharges = collect();
        $now = Carbon::now();

        foreach ($activeSurcharges as $surcharge) {
            $isApplicable = false;

            // Check if time range is set
            if ($surcharge->time_from && $surcharge->time_to) {
                // Parse time_from and time_to
                $startTime = Carbon::parse($surcharge->time_from->format('H:i'));
                $endTime = Carbon::parse($surcharge->time_to->format('H:i'));
                $currentTime = Carbon::parse($now->format('H:i'));

                // Handle overnight ranges (e.g., 22:00 to 05:00)
                if ($startTime->gt($endTime)) {
                    if ($currentTime->gte($startTime) || $currentTime->lte($endTime)) {
                        $isApplicable = true;
                    }
                } else {
                    // Normal range (e.g., 08:00 to 17:00)
                    if ($currentTime->between($startTime, $endTime)) {
                        $isApplicable = true;
                    }
                }
            } else {
                // If no specific time range, applying it assuming it always applies if active
                 $isApplicable = true;
            }

            if ($isApplicable) {
                $applicableSurcharges->push($surcharge);
            }
        }
        return $applicableSurcharges;
    }

    public function attachSurcharges(Request $request, $surcharges)
    {
        $surchargesToAttach = [];
        foreach ($surcharges as $surcharge) {
            $surchargesToAttach[$surcharge->id] = ['amount' => $surcharge->amount];
        }
        
        if (!empty($surchargesToAttach)) {
            $request->surcharges()->syncWithoutDetaching($surchargesToAttach);
        }
    }

    // public function calculateTotalSurcharge(?Request $request = null)
    // {
    //     $applicableSurcharges = $this->getApplicableSurcharges();
    //     $totalSurcharge = $applicableSurcharges->sum('amount');

    //     if ($request) {
    //         $this->attachSurcharges($request, $applicableSurcharges);
    //     }

    //     return $totalSurcharge;
    // }
}
