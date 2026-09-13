<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Request as TripRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class DriverSettlementService{
    public function calculateDriverBalance(int $driverId): array{

        $driver = Driver::find($driverId);
        if (!$driver) {
            throw new Exception('السائق غير موجود في النظام.', 404);
        }
        $cashTripsCommission = TripRequest::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->whereNull('driver_settled_at')
            ->sum('app_commission_amount');
        
        $digitalTrips = TripRequest::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->whereIn('payment_method', ['wallet', 'digital_payment'])
            ->whereNull('driver_settled_at')
            ->selectRaw('SUM(final_price) as total_collected, SUM(app_commission_amount) as total_commission')
            ->first();
        
        $collectedByApp = (float) ($digitalTrips->total_collected ?? 0);
        $appDigitalCommission = (float) ($digitalTrips->total_commission ?? 0);
        $dueToDriver = $collectedByApp - $appDigitalCommission;
        $netBalance = $dueToDriver - $cashTripsCommission;
        return [
            'driver_owes_app' => round((float)$cashTripsCommission, 2),
            'app_owes_driver' => round((float)$dueToDriver, 2),
            'net_balance' => round((float)$netBalance, 2),
            'settlement_action' => $netBalance >= 0 ? 'app_pays_driver' : 'driver_pays_app',
        ];
    }
    public function settleAccounts(int $driverId, ?int $adminId = null): array
    {
        return DB::transaction(function () use ($driverId, $adminId) {
            $balance = $this->calculateDriverBalance($driverId);
            $updatedRows = TripRequest::where('driver_id', $driverId)
                ->where('status', 'completed')
                ->whereNull('driver_settled_at')
                ->update([
                    'driver_settled_at' => now(),
                ]);

            return [
                'settled_trips_count' => $updatedRows,
                'final_settled_amount' => $balance['net_balance'],
                'action_taken' => $balance['settlement_action'],
                'settled_at'  => now()->toDateTimeString(),
            ];
        });
    }

    public function getDriverStats(int $driverId)
    {
        $driver = Driver::find($driverId);
        if (!$driver) {
            throw new Exception('السائق غير موجود في النظام.', 404);
        }
        $tripsStats = TripRequest::where('driver_id', $driverId)
        ->selectRaw("
            COUNT(id) as total_trips,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_trips,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_trips,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN final_price ELSE 0 END), 0) as total_earnings
        ")->first();
        $balance = $this->calculateDriverBalance($driverId);
        return [
            'trips' => [
                'total_trips' => (int) $tripsStats->total_trips,
                'completed_trips' => (int) $tripsStats->completed_trips,
                'cancelled_trips'=> (int) $tripsStats->cancelled_trips,
            ],
            'financial' => [
                'total_earnings' => round((float) $tripsStats->total_earnings, 2),
                'driver_owes_app' => $balance['driver_owes_app'], // عمولة الكاش المطلوب توريدها للمنصة
                'app_owes_driver'=> $balance['app_owes_driver'], // مستحقات السائق من الدفع الإلكتروني/المحفظة
                'net_balance' => $balance['net_balance'],     // الرصيد الصافي
                'settlement_action'=> $balance['settlement_action'], // app_pays_driver أو driver_pays_app
            ]
        ];

    }
}