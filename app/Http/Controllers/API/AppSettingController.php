<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use Illuminate\Support\Requests\Facade\Storage;

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

        // 1. توليد الاسم
        $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.pdf'; 

        // 2. التخزين في مجلد pdfs داخل القرص public
        $path = $file->storeAs('pdfs', $filename, 'public');

        // 3. إنشاء الرابط الصحيح باستخدام Storage URL
        // هذه الطريقة هي الأفضل لأنها تضيف /storage تلقائياً
        $fullUrl = Storage::url($path);
        
        // أو إذا أردت استخدام asset بشكل مباشر:
        // $fullUrl = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'url' => $fullUrl
        ], 200);
    }
}

    return response()->json(['error' => 'File not uploaded'], 400);
}
}
