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
        $fullUrl = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'url' => $fullUrl
        ], 200);
    }

    return response()->json(['error' => 'File not uploaded'], 400);
}

public function receiveBackup(Request $request)
{
    // 1. التحقق من وجود الملف واسم العميل
    if ($request->hasFile('backup_file')) {
        try {
            // جلب اسم العميل وتطهيره من أي رموز غير مسموحة في أسماء المجلدات
            $clientName = $request->input('client_name', 'default_client');
            $clientFolder = Str::slug($clientName, '_'); // يحول الاسم إلى صيغة آمنة مثل client_ahmed_store

            $file = $request->file('backup_file');
            
            if (!$file->isValid()) {
                return response()->json(['message' => 'File is corrupted'], 400);
            }

            $filename = $file->getClientOriginalName();
            
            // 2. تحديد المسار الجديد: backups/اسم_العميل/الملف
            // المسار سيكون: storage/app/backups/client_ahmed_store/backup-xxx.sql
            $path = $file->storeAs("backups/{$clientFolder}", $filename);

            return response()->json([
                'status' => true,
                'message' => "تم استلام النسخة وحفظها في مجلد العميل: {$clientName}",
                'path' => $path
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    return response()->json(['message' => 'No backup file provided'], 400);
}
}
