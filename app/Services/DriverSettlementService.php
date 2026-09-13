<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Request as TripRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class DriverSettlementService{
    public function calculateDriverBalance(int $driverId): array{

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
}