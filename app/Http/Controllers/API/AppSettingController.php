<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Classes\ApiResponseClass;

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
    public function uploadPdf(Request $request)
{
    if ($request->hasFile('file')) {
        $file = $request->file('file');

        // 1. توليد اسم عشوائي ولكن مع التأكد من وجود الامتداد .pdf
        // نستخدم time() لضمان عدم تكرار الاسم + الامتداد الأصلي
        $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.pdf'; 

        // 2. استخدام storeAs بدلاً من store لتحديد الاسم
        $path = $file->storeAs('pdfs', $filename, 'public');

        // 3. إنشاء الرابط
        $fullUrl = asset($path);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'url' => $fullUrl
        ], 200);
    }

    return response()->json(['error' => 'File not uploaded'], 400);
}
}
