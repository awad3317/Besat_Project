<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Repositories\AppSettingRepository;

class AppSettingController extends Controller
{
    public function __Construct(private AppSettingRepository $appSettingRepository)
    {
        //
    }

    public function index()
    {
        try {
            $settings = Cache::rememberForever('app_settings_user', function () {
                return $this->appSettingRepository->getSetting('user');
            });
            return ApiResponseClass::sendResponse($settings, 'User app settings retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::sendError('Failed to retrieve app settings', $e->getMessage(), 500);
        }
    }
    public function driverSettings()
    {
        try {
            $settings = Cache::rememberForever('app_settings_driver', function () {
                return $this->appSettingRepository->getSetting('driver');
            });
            return ApiResponseClass::sendResponse($settings, 'Driver app settings retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::sendError('Failed to retrieve driver app settings', $e->getMessage(), 500);
        }
    }
}
