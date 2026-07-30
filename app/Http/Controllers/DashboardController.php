<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Services\FirebaseService;
use App\Repositories\UserRepository;
use App\Repositories\DriverRepository;
use App\Repositories\RequestRepository;
use App\Repositories\SpecialOrderRepository;

class DashboardController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private DriverRepository $driverRepository, 
        private SpecialOrderRepository $specialOrderRepository,
        private RequestRepository $requestRepository
        )
    {
        
    }

    /**
     * إرسال إشعار ترحيبي تجريبي لجميع الأجهزة المسجلة (للاختبار فقط)
     */
    public function sendTestNotification(FirebaseService $firebaseService)
    {
        try {
            $deviceTokens = UserDevice::whereNotNull('device_token')
                ->where('device_token', '!=', '')
                ->pluck('device_token')
                ->unique()
                ->values()
                ->toArray();

            if (empty($deviceTokens)) {
                return redirect()->back()->with([
                    'error' => true,
                    'error_message' => 'لا توجد أجهزة مسجلة لإرسال الإشعار إليها.',
                ]);
            }

            $title = '🎉 مرحباً بك في بيسات!';
            $body = 'هذا إشعار تجريبي للترحيب بك. شكراً لاستخدامك تطبيقنا!';
            $data = [
                'type' => 'test_welcome',
                'timestamp' => now()->toIso8601String(),
            ];

            $successCount = 0;
            $failCount = 0;

            foreach ($deviceTokens as $token) {
                try {
                    $firebaseService->sendNotification($token, $title, $body, $data);
                    $successCount++;
                } catch (\Exception $e) {
                    $failCount++;
                    \Log::warning("Failed to send test notification to token: {$token}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->back()->with([
                'success' => true,
                'success_title' => 'تم الإرسال!',
                'success_message' => "تم إرسال الإشعار التجريبي بنجاح إلى {$successCount} جهاز." 
                    . ($failCount > 0 ? " (فشل: {$failCount})" : ''),
            ]);

        } catch (\Exception $e) {
            \Log::error('Test notification failed: ' . $e->getMessage());

            return redirect()->back()->with([
                'error' => true,
                'error_message' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage(),
            ]);
        }
    }
    
    public function index()
    {
        $salesData = $this->getRealSalesData();
        $totalUsers=$this->userRepository->getUsers()->count();
        $totalDrivers=$this->driverRepository->index()->count();
        $totalSpecialOrders = $this->specialOrderRepository->index()->count();
        $totalRequests= $this->requestRepository->index()->count();
        
        return view('pages.dashboard.index', [
            'monthlySales' => $salesData,
            'chartTitle' => 'المبيعات الشهرية الحقيقية',
            'totalUsers' => $totalUsers,
            'totalDrivers' => $totalDrivers,
            'totalSpecialOrders' => $totalSpecialOrders,
            'totalRequests' => $totalRequests
        ]);
        // return view('');
    }
    private function getRealSalesData()
    {
        // هنا تجلب البيانات الحقيقية من قاعدة البيانات
        // مثال:
        return [
            'january' => 1250,
            'february' => 1320,
            'march' => 1180,
            'april' => 1400,
            'may' => 1350,
            'june' => 1280,
            'july' => 1450,
            'august' => 1380,
            'september' => 1420,
            'october' => 1500,
            'november' => 1480,
            'december' => 1600
        ];

        // أو من قاعدة البيانات:
        // return Sale::selectRaw('MONTH(sale_date) as month, SUM(amount) as total')
        //     ->whereYear('sale_date', date('Y'))
        //     ->groupBy('month')
        //     ->pluck('total', 'month')
        //     ->toArray();
    }
}