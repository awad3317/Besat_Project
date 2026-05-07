<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
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
            $settings = $this->appSettingRepository->getSetting();
            return ApiResponseClass::sendResponse($settings, 'App settings retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseClass::sendError('Failed to retrieve app settings', $e->getMessage(), 500);
        }
    }
}
