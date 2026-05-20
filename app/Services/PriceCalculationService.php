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


    public function getdistanceInKm(float $lat1, float $lon1, float $lat2, float $lon2, array $stops = [])
    {
        $apiKey = env('GOOGLE_MAP_KEY');
        if (!$apiKey) {
            Log::error('Google Maps API Key is not configured.');
            return null;
        }
        $points = [];
        $points[] = "{$lat1},{$lon1}"; 

        foreach ($stops as $stop) {
            $points[] = "{$stop['latitude']},{$stop['longitude']}";
        }
        $points[] = "{$lat2},{$lon2}"; 
        $totalDistanceMeters = 0;
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
        for ($i = 0; $i < count($points) - 1; $i++) {
            $response = Http::get($url, [
                'origins'      => $points[$i],
                'destinations' => $points[$i + 1],
                'mode'         => 'driving',
                'key'          => $apiKey,
            ]);

            if ($response->failed()) {
                Log::error('Google Distance Matrix API request failed at segment ' . $i);
                return null;
            }

            $data = $response->json();

            if ($data['status'] == 'OK' && $data['rows'][0]['elements'][0]['status'] == 'OK') {
                $totalDistanceMeters += $data['rows'][0]['elements'][0]['distance']['value'];
            } else {
                Log::warning('Google Distance Matrix API error status at segment ' . $i . ': ' . $data['status']);
                return null;
            }
        }
        return (float) ($totalDistanceMeters / 1000);
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
        return round($commission_amount, 2);
    }

    public function calculateSurcharges($tripDatetime)
    {
        $tripTime = Carbon::parse($tripDatetime);
        $currentTimeString = $tripTime->format('H:i');

        $activeSurcharges = Surcharge::where('is_active', true)->get();

        $total_amount = 0;
        $details = [];

        foreach ($activeSurcharges as $surcharge) {
            $applyThisSurcharge = false;

            if ($surcharge->type === 'time_based' && $surcharge->time_from && $surcharge->time_to) {
                $from = Carbon::parse($surcharge->time_from)->format('H:i');
                $to = Carbon::parse($surcharge->time_to)->format('H:i');

                if ($from > $to) { // عبور منتصف الليل
                    if ($currentTimeString >= $from || $currentTimeString <= $to) {
                        $applyThisSurcharge = true;
                    }
                } else { // وقت عادي
                    if ($currentTimeString >= $from && $currentTimeString <= $to) {
                        $applyThisSurcharge = true;
                    }
                }
            } elseif ($surcharge->type === 'conditional') {
                $applyThisSurcharge = true;
            }

            if ($applyThisSurcharge) {
                $total_amount += $surcharge->amount;
                $details[] = [
                    'id'     => $surcharge->id,
                    'name'   => $surcharge->name,
                    'amount' => (float) $surcharge->amount
                ];
            }
        }

        return [
            'total_amount' => (float) $total_amount,
            'details'      => $details
        ];
    }

    public function attachSurcharges(Request $request, $surchargesDetailsArray)
    {
        $surchargesToAttach = [];
        foreach ($surchargesDetailsArray as $surcharge) {
            $surchargesToAttach[$surcharge['id']] = ['amount' => $surcharge['amount']]; 
        }
        
        if (!empty($surchargesToAttach)) {
            $request->surcharges()->syncWithoutDetaching($surchargesToAttach);
        }
    }

    public function getFullPriceDetails(array $validatedData, $vehicle, $couponObject = null)
    {
        $stops = $validatedData['stops'] ?? [];
        $distanceInKm = $this->getdistanceInKm(
            $validatedData['start_latitude'],
            $validatedData['start_longitude'],
            $validatedData['end_latitude'],
            $validatedData['end_longitude'],
            $stops 
        );

        if ($distanceInKm === null) {
            throw new \Exception("فشل في حساب المسافة الدقيقة من خرائط جوجل.");
        }
        $price_per_km = $this->getPricePerKmByDistanceAndVehicle($distanceInKm, $vehicle);
        $base_price = $this->calculatePrice($distanceInKm, $price_per_km, $vehicle->min_price);
        
        $surcharges_details = [];
        $ac_cost = 0;
        $ac_applied = false;
        $wants_ac = $validatedData['wants_ac'] ?? false;
        
        if ($wants_ac && $vehicle->has_ac_option) {
            $ac_cost = $distanceInKm * $vehicle->ac_price_per_km;
            $ac_cost = round((float) $ac_cost, 2);
            $ac_applied = true;
        
            $surcharges_details[] = [
                'id'     => 'ac_cost',
                'name'   => 'رسوم تشغيل التكييف',
                'amount' => $ac_cost
            ];
        }

        $tripDatetime = $validatedData['trip_datetime'] ?? now()->format('Y-m-d H:i:s');
        $surchargesData = $this->calculateSurcharges($tripDatetime);
        $total_surcharge_amount = $surchargesData['total_amount'];
        
        if (!empty($surchargesData['details'])) {
            $surcharges_details = array_merge($surcharges_details, $surchargesData['details']);
        }

        $original_price = $base_price + $ac_cost + $total_surcharge_amount;
        $final_price = $original_price;

        $discount_amount = 0;
        $coupon_for_response = null;
        
        if ($couponObject) {
            $coupon_rate = $couponObject->discount_rate;
            $coupon_for_response = number_format($coupon_rate * 100, 2) . '%';
            $discount_amount = $original_price * $coupon_rate;
            $discount_amount = round((float) $discount_amount, 2);
            $final_price = $original_price - $discount_amount;

            $surcharges_details[] = [
                'id'     => 'discount',
                'name'   => "خصم قسيمة ({$coupon_for_response})",
                'amount' => -$discount_amount 
            ];
        }
        
        $app_commission_amount = $this->calculateCommission($final_price);
        
        return [
            'distance_in_km'        => round((float) $distanceInKm, 2),
            'base_price'            => round((float) $base_price, 2),
            'original_price'        => round((float) $original_price, 2),
            'final_price'           => round((float) $final_price, 2),
            'vehicle'               => $vehicle->type,
            'coupon'                => $coupon_for_response,
            'ac_applied'            => $ac_applied,
            'ac_cost'               => round((float) $ac_cost, 2),
            'total_surcharges'      => round((float) $total_surcharge_amount, 2),
            'discount_amount'       => round((float) $discount_amount, 2),
            'app_commission_amount' => round((float) $app_commission_amount, 2),
            'surcharges_details'    => $surcharges_details 
        ];
    }
}
