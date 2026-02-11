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

        // 2. التحقق من وجود الملف
        if ($request->hasFile('backup_file')) {
            try {
                $file = $request->file('backup_file');
                
                // التأكد من أن الملف صالح
                if (!$file->isValid()) {
                    return response()->json(['message' => 'File is corrupted'], 400);
                }

                // 3. تحديد اسم ومسار الحفظ
                // نستخدم الاسم الأصلي للملف القادم من العميل (يحتوي على التاريخ)
                $filename = $file->getClientOriginalName();
                
                // هام: نحفظه في مجلد local (وليس public) لحمايته من التحميل المباشر
                // المسار النهائي سيكون: storage/app/backups/backup-2024-02-11-xxxxx.sql
                $path = $file->storeAs('backups', $filename); // الافتراضي هو الـ disk 'local'

                return response()->json([
                    'status' => true,
                    'message' => 'تم استلام وحفظ النسخة الاحتياطية بنجاح',
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
