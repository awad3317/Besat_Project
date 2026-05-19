<?php

namespace App\Services;

use App\Models\Surcharge;
use Carbon\Carbon;

class SurchargeService
{
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
}