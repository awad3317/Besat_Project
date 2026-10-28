<?php

namespace App\Http\Controllers;

use App\Repositories\AppSettingRepository;

class SystemSettingsController extends Controller
{
    public function __construct(private AppSettingRepository $appSettingRepository)
    {
    }

    public function index()
    {
        $settings = $this->appSettingRepository->getSetting();
        return view('pages.systems.index', compact('settings'));
    }
}
