<?php

namespace App\Services;

use App\Repositories\AppSettingRepository;
use Illuminate\Support\Facades\Cache;

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
     * دالة توجيه الطلب للسائقين القريبين
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
                    'request_id'      => $tripRequest->id,
                    'start_latitude'  => $tripRequest->start_latitude,
                    'start_longitude' => $tripRequest->start_longitude,
                    'start_address'   => $tripRequest->start_address,
                ];

                foreach ($nearestDrivers as $driver) {
                    if (!empty($driver->device_token)) {
                        $this->firebaseService->sendNotification($driver->device_token, $title, $body, $data);
                    }
                }
            }
            return true; // تم الإرسال بنجاح
        } else {
            // النظام اليدوي - إرسال الطلب للداشبورد فقط
            return false;
        }
    }
}