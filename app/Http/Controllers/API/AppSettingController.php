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
        
        // $request->validate([
        //     'file' => 'required|mimes:pdf|max:10240', // Max 10MB
        // ]);

        if ($request->hasFile('file')) {
            // 2. تخزين الملف في مجلد public/pdfs
            // يتم تخزينه في storage/app/public/pdfs
            $path = $request->file('file')->store('pdfs', 'public');

            // 3. إنشاء الرابط الكامل للملف
            // الدالة asset تقوم بإنشاء رابط يبدأ بـ http://your-domain.com
            $fullUrl = asset('storage/' . $path);

            // 4. إرجاع الرابط كـ JSON
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'url' => $fullUrl
            ], 200);
        }

        return response()->json(['error' => 'File not uploaded'], 400);
    }
}
