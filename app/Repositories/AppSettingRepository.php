<?php

namespace App\Repositories;
use App\Models\app_setting;

class AppSettingRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getSetting(string $appType = 'user')
    {
        return app_setting::where('app_type', $appType)->first();
    }
}
