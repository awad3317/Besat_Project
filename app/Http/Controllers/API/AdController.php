<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\AdRepository;
use App\Classes\ApiResponseClass;
use Exception;

class AdController extends Controller
{
    public function __construct(private AdRepository $adRepository)
    {
        
    }

    public function getAds()
    {
        try {
            $ads = $this->adRepository->getActiveAdsForMobile();
            return ApiResponseClass::sendResponse($ads, 'تم جلب الإعلانات بنجاح.'); 
        } catch (Exception $e) {
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب الإعلانات.', $e->getMessage(), 500);
        }
    }
}