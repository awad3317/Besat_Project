<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\DriverSettlement;
use App\Models\Request as TripRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class DriverSettlementService
{
    /**
     * حساب رصيد السائق للرحلات غير المصفاة
     */
    public function calculateDriverBalance(int $driverId): array
    {
        $driver = Driver::find($driverId);
        if (!$driver) {
            throw new Exception('السائق غير موجود في النظام.', 404);
        }
        $cashTrips = TripRequest::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->whereNull('driver_settlement_id')
            ->selectRaw('
                COALESCE(SUM(app_commission_amount - discount_amount), 0) as net_cash_adjustment,
                COALESCE(SUM(app_commission_amount), 0) as gross_commission,
                COALESCE(SUM(discount_amount), 0) as total_discount_subsidized
            ')
            ->first();

        $cashNetAdjustment = (float) ($cashTrips->net_cash_adjustment ?? 0);

        $digitalTrips = TripRequest::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->whereIn('payment_method', ['wallet', 'digital_payment'])
            ->whereNull('driver_settlement_id')
            ->selectRaw('
                COALESCE(SUM(original_price - app_commission_amount), 0) as total_driver_due,
                COALESCE(SUM(final_price), 0) as total_collected_by_app,
                COALESCE(SUM(app_commission_amount), 0) as total_commission,
                COALESCE(SUM(discount_amount), 0) as total_discount_subsidized
            ')
            ->first();

        $digitalDriverDue = (float) ($digitalTrips->total_driver_due ?? 0);

        // 3. صافي الحساب (Net Balance)
        $netBalance = $digitalDriverDue - $cashNetAdjustment;

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

    /**
     * إتمام التسوية وتوليد السجل وربط الطلبات
     */
    public function settleAccounts(int $driverId, ?int $adminId = null): array
    {
        return DB::transaction(function () use ($driverId, $adminId) {
            $balance = $this->calculateDriverBalance($driverId);

            // جلب معرّفات الطلبات الجاهزة للتسوية وقفلها للتحديث
            $pendingTripIds = TripRequest::where('driver_id', $driverId)
                ->where('status', 'completed')
                ->whereNull('driver_settlement_id')
                ->lockForUpdate()
                ->pluck('id');

            if ($pendingTripIds->isEmpty()) {
                throw new Exception('لا توجد رحلات معلقة تحتاج إلى تصفية حساب لهذا السائق.', 422);
            }

            // 1. إنشاء سجل التصفية الرئيسي
            $settlement = DriverSettlement::create([
                'driver_id'         => $driverId,
                'admin_id'          => $adminId,
                'trips_count'       => $pendingTripIds->count(),
                'driver_owes_app'   => $balance['driver_owes_app'],
                'app_owes_driver'   => $balance['app_owes_driver'],
                'net_balance'       => $balance['net_balance'],
                'settlement_action' => $balance['settlement_action'],
            ]);

            // 2. تحديث الطلبات وربطها بالتصفية المنشأة
            TripRequest::whereIn('id', $pendingTripIds)->update([
                'driver_settlement_id' => $settlement->id,
                'driver_settled_at'    => now(),
                'driver_settled_by'    => $adminId,
            ]);

            return [
                'settlement_id'        => $settlement->id,
                'settled_trips_count'  => $pendingTripIds->count(),
                'final_settled_amount' => $balance['net_balance'],
                'action_taken'         => $balance['settlement_action'],
                'settled_at'           => now()->toDateTimeString(),
            ];
        });
    }

    /**
     * إحصائيات السائق العامة والمالية
     */
    public function getDriverStats(int $driverId): array
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