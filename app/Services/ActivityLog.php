<?php

namespace App\Services;

use App\Models\activity_log;
use Illuminate\Http\Request;

class ActivityLog
{
    public static function log($action,$model_name = null,$description = null) {
        $adminId = auth()->id();
        if (!$adminId) {
            return null;
        }

        return activity_log::create([
            'admin_id' => $adminId,
            'action' => $action,
            'model_name'=>$model_name,
            'description' => $description,
        ]);
    }
}