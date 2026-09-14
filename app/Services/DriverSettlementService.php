<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Request as TripRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class DriverSettlementService
{
    public function calculateDriverBalance(int $driverId): array
    {
        $driver = Driver::find($driverId);
        if (!$driver) {
            throw new Exception('السائق غير موجود في النظام.', 404);
        }

        // 1. حساب تسوية الرحلات النقدية (Cash Trips)
        // السائق استلم كاش = final_price
        // حق السائق = original_price - app_commission_amount
        // ذمة السائق الصافية من الكاش = final_price - حق السائق = app_commission_amount - discount_amount
        $cashTrips = TripRequest::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->whereNull('driver_settled_at')
            ->selectRaw('
                COALESCE(SUM(app_commission_amount - discount_amount), 0) as net_cash_adjustment,
                COALESCE(SUM(app_commission_amount), 0) as gross_commission,
                COALESCE(SUM(discount_amount), 0) as total_discount_subsidized
            ')
            ->first();

        $cashNetAdjustment = (float) ($cashTrips->net_cash_adjustment ?? 0);

        // 2. حساب تسوية الرحلات الرقمية (Wallet / Digital Payment)
        // التطبيق استلم المبلغ نيابة عن السائق
        // حق السائق الصافي = original_price - app_commission_amount (المنصة تمتص الكوبون ولا تخصمه من السائق)
        $digitalTrips = TripRequest::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->whereIn('payment_method', ['wallet', 'digital_payment'])
            ->whereNull('driver_settled_at')
            ->selectRaw('
                COALESCE(SUM(original_price - app_commission_amount), 0) as total_driver_due,
                COALESCE(SUM(final_price), 0) as total_collected_by_app,
                COALESCE(SUM(app_commission_amount), 0) as total_commission,
                COALESCE(SUM(discount_amount), 0) as total_discount_subsidized
            ')
            ->first();

        $digitalDriverDue = (float) ($digitalTrips->total_driver_due ?? 0);

        // 3. صافي الحساب (Net Balance):
        // مستحقات السائق من الدفع الرقمي ناقص ما تبقى عليه من الرحلات النقدية
        // Net Balance = Digital Driver Due - Cash Net Adjustment
        $netBalance = $digitalDriverDue - $cashNetAdjustment;

        // فصل المطالبات بحسب اتجاه الذمة المالية
        $driverOwesApp = $cashNetAdjustment > 0 ? $cashNetAdjustment : 0.0;
        $appOwesDriver = $digitalDriverDue + ($cashNetAdjustment < 0 ? abs($cashNetAdjustment) : 0.0);

        return [
            'driver_owes_app'   => round($driverOwesApp, 2),
            'app_owes_driver'   => round($appOwesDriver, 2),
            'net_balance'       => round($netBalance, 2),
            'settlement_action' => $netBalance >= 0 ? 'app_pays_driver' : 'driver_pays_app',
            'coupon_subsidized' => round((float) (($cashTrips->total_discount_subsidized ?? 0) + ($digitalTrips->total_discount_subsidized ?? 0)), 2),
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
                    'driver_settled_by' => $adminId,
                ]);

            return [
                'settled_trips_count'  => $updatedRows,
                'final_settled_amount' => $balance['net_balance'],
                'action_taken'         => $balance['settlement_action'],
                'settled_at'           => now()->toDateTimeString(),
            ];
        });
    }

    public function getDriverStats(int $driverId): array
    {
        $driver = Driver::find($driverId);
        if (!$driver) {
            throw new Exception('السائق غير موجود في النظام.', 404);
        }

        // إجمالي دخل السائق الحقيقي المحسوب على السعر الأصلي وليس المخفض
        $tripsStats = TripRequest::where('driver_id', $driverId)
            ->selectRaw("
                COUNT(id) as total_trips,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_trips,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_trips,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN (original_price - app_commission_amount) ELSE 0 END), 0) as total_net_earnings
            ")
            ->first();

        $balance = $this->calculateDriverBalance($driverId);

        return [
            'trips' => [
                'total_trips'     => (int) $tripsStats->total_trips,
                'completed_trips' => (int) $tripsStats->completed_trips,
                'cancelled_trips' => (int) $tripsStats->cancelled_trips,
            ],
            'financial' => [
                'total_earnings'    => round((float) $tripsStats->total_net_earnings, 2),
                'driver_owes_app'   => $balance['driver_owes_app'],
                'app_owes_driver'   => $balance['app_owes_driver'],
                'net_balance'       => $balance['net_balance'],
                'settlement_action' => $balance['settlement_action'],
            ]
        ];
    }
}