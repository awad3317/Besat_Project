<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // استعلام واحد سريع جداً لجلب جميع إحصائيات الكروت العلوية من قاعدة البيانات الفعلية
        $counts = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM users WHERE type = 'user' AND deleted_at IS NULL) AS total_users,
                (SELECT COUNT(*) FROM drivers) AS total_drivers,
                (SELECT COUNT(*) FROM requests WHERE (payment_method NOT IN ('digital_payment', 'deposit') OR payment_status = 'paid')) AS total_requests,
                (SELECT COUNT(*) FROM service_bookings) AS total_special_orders
        ");

        $salesData = $this->getMonthlyRequestsData();
        $topRatedDrivers = $this->getTopRatedDrivers();

        return view('pages.dashboard.index', [
            'monthlySales'       => $salesData,
            'chartTitle'         => 'إحصائيات الطلبات الشهرية الفعلية',
            'totalUsers'         => (int) ($counts->total_users ?? 0),
            'totalDrivers'       => (int) ($counts->total_drivers ?? 0),
            'totalSpecialOrders' => (int) ($counts->total_special_orders ?? 0),
            'totalRequests'      => (int) ($counts->total_requests ?? 0),
            'topRatedDrivers'    => $topRatedDrivers,
        ]);
    }

    /**
     * جلب عدد الطلبات الفعلية خلال أشهر السنة الحالية في استعلام تجميعي واحد.
     */
    private function getMonthlyRequestsData(): array
    {
        $monthlyCounts = DB::table('requests')
            ->selectRaw('MONTH(created_at) as month_num, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->where(function ($q) {
                $q->whereNotIn('payment_method', ['digital_payment', 'deposit'])
                    ->orWhere('payment_status', 'paid');
            })
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month_num');

        return [
            'january'   => (int) ($monthlyCounts[1] ?? 0),
            'february'  => (int) ($monthlyCounts[2] ?? 0),
            'march'     => (int) ($monthlyCounts[3] ?? 0),
            'april'     => (int) ($monthlyCounts[4] ?? 0),
            'may'       => (int) ($monthlyCounts[5] ?? 0),
            'june'      => (int) ($monthlyCounts[6] ?? 0),
            'july'      => (int) ($monthlyCounts[7] ?? 0),
            'august'    => (int) ($monthlyCounts[8] ?? 0),
            'september' => (int) ($monthlyCounts[9] ?? 0),
            'october'   => (int) ($monthlyCounts[10] ?? 0),
            'november'  => (int) ($monthlyCounts[11] ?? 0),
            'december'  => (int) ($monthlyCounts[12] ?? 0),
        ];
    }

    /**
     * جلب أعلى 3 سائقين تقييماً من قاعدة البيانات مباشرة عبر استعلام واحد خفيف بدون N+1.
     */
    private function getTopRatedDrivers()
    {
        return DB::table('ratings')
            ->join('drivers', 'ratings.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicles', 'drivers.vehicle_id', '=', 'vehicles.id')
            ->select([
                'drivers.id',
                'drivers.name',
                'drivers.phone',
                'drivers.city',
                'drivers.driver_image',
                'vehicles.type as vehicle_type',
                DB::raw('AVG(ratings.rating_value) as avg_rating'),
                DB::raw('COUNT(ratings.id) as ratings_count'),
            ])
            ->groupBy([
                'drivers.id',
                'drivers.name',
                'drivers.phone',
                'drivers.city',
                'drivers.driver_image',
                'vehicles.type',
            ])
            ->orderByDesc('avg_rating')
            ->orderByDesc('ratings_count')
            ->limit(3)
            ->get();
    }
}