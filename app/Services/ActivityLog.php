<?php

namespace App\Services;

use App\Models\activity_log;
use Illuminate\Http\Request;

class ActivityLog
{
    public static function log($action, $model_name = null, $description = null, $model_id = null) {
        $adminId = auth()->id();
        if (!$adminId) {
            return null;
        }

        // دعم التمرير المرن سواء كان model_id بالمعامل الثالث أو الرابع
        if (is_numeric($description) && (is_string($model_id) || is_null($model_id))) {
            $temp = $description;
            $description = $model_id;
            $model_id = $temp;
        }

        return activity_log::create([
            'admin_id'   => $adminId,
            'action'     => $action,
            'model_name' => $model_name,
            'model_id'   => $model_id,
            'description'=> $description,
        ]);
    }
}