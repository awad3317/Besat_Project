<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminNotification;
use App\Repositories\AppSettingRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TripDispatchService
{
    protected $driverLocationService;
    protected $firebaseService;
    protected $appSettingRepository;

    // حقن الاعتماديات (Dependency Injection)
    public function __construct(
        DriverLocationService $driverLocationService,
        FirebaseService $firebaseService,
        AppSettingRepository $appSettingRepository
    ) {
        $this->driverLocationService = $driverLocationService;
        $this->firebaseService = $firebaseService;
        $this->appSettingRepository = $appSettingRepository;
    }

    /**
     * دالة توجيه الطلب للسائقين القريبين أو إرسال إشعار للداشبورد في النظام اليدوي
     */
    public function dispatchToDrivers($tripRequest)
    {
        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });

        if ($appSettings && $appSettings->auto_assign_to_drivers) {
            // جلب السائقين القريبين بناءً على إحداثيات الطلب
            $nearestDrivers = $this->driverLocationService->getNearestDrivers(
                $tripRequest->start_latitude, 
                $tripRequest->start_longitude, 
                8, 
                20
            );
            
            if ($nearestDrivers) {
                $title = 'طلب جديد';
                $body  = 'يوجد طلب جديد في منطقتك، اضغط لقبول الطلب';
                $data  = [
                    'new_request'     => 'true',
                    'new_message'     => 'false',
                    'request_id'      => (string) $tripRequest->id,
                    'start_latitude'  => (string) $tripRequest->start_latitude,
                    'start_longitude' => (string) $tripRequest->start_longitude,
                    'start_address'   => (string) $tripRequest->start_address,
                ];

                foreach ($nearestDrivers as $driver) {
                    if (!empty($driver->device_token)) {
                        try {
                            $this->firebaseService->sendNotification($driver->device_token, $title, $body, $data);
                        } catch (\Exception $e) {
                            Log::error("Failed to send FCM to driver {$driver->id}: " . $e->getMessage());
                        }
                    }
                }
            }
            return true; // تم الإرسال بنجاح
        } else {
            // النظام اليدوي - إرسال الطلب للداشبورد فقط
            $this->notifyAdmins($tripRequest);
            return false;
        }
    }

    /**
     * إرسال إشعار للداشبورد (حسابات الأدمن) عبر Firebase وحفظه في قاعدة البيانات
     */
    protected function notifyAdmins($tripRequest): void
    {
        $admins = User::whereIn('type', ['admin', 'superAdmin'])
            ->where('is_banned', false)
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $title = 'طلب رحلة جديد #' . $tripRequest->id;
        $body  = 'يوجد طلب رحلة جديد من (' . ($tripRequest->start_address ?? 'غير محدد') . ') إلى (' . ($tripRequest->end_address ?? 'غير محدد') . ') بانتظار تعيين سائق.';
        $url   = route('request.show', $tripRequest->id);

        $data = [
            'new_request'     => 'true',
            'new_message'     => 'false',
            'request_id'      => (string) $tripRequest->id,
            'start_latitude'  => (string) ($tripRequest->start_latitude ?? ''),
            'start_longitude' => (string) ($tripRequest->start_longitude ?? ''),
            'start_address'   => (string) ($tripRequest->start_address ?? ''),
            'end_address'     => (string) ($tripRequest->end_address ?? ''),
            'url'             => (string) $url,
        ];

        $sentTokens = [];

        foreach ($admins as $admin) {
            try {
                $admin->notify(new AdminNotification($tripRequest, $title, $body, $url));
            } catch (\Exception $e) {
                Log::error("Failed to store database notification for admin {$admin->id}: " . $e->getMessage());
            }

            if (!empty($admin->fcm_token) && !in_array($admin->fcm_token, $sentTokens, true)) {
                $sentTokens[] = $admin->fcm_token;
                try {
                    $this->firebaseService->sendNotification($admin->fcm_token, $title, $body, $data);
                } catch (\Exception $e) {
                    Log::error("Failed to send FCM to admin {$admin->id}: " . $e->getMessage());
                }
            }
        }
    }
}

}