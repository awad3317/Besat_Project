<?php

namespace App\Services;

use App\Models\activity_log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLog
{
    public static function log($action, $model_name = null, $description = null, $model_id = null)
    {
        try {
            // محاولة جلب معرف المسؤول من أي guard مسجل
            $adminId = Auth::id() ?? auth()->id() ?? auth('web')->id();
            if (!$adminId) {
                Log::warning("ActivityLog: لم يتم العثور على مسؤول مسجل دخول (auth()->id() is null) للعملية [{$action}]");
                return null;
            }

            // دعم التمرير المرن سواء كان model_id بالمعامل الثالث أو الرابع
            if (is_numeric($description) && (is_string($model_id) || is_null($model_id))) {
                $temp = $description;
                $description = $model_id;
                $model_id = $temp;
            }

            return activity_log::create([
                'admin_id'    => $adminId,
                'action'      => $action,
                'model_name'  => $model_name ?? 'General',
                'model_id'    => $model_id,
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::error("ActivityLog Exception: " . $e->getMessage(), [
                'action'     => $action,
                'model_name' => $model_name,
                'model_id'   => $model_id,
            ]);
            return null;
        }
    }
}
