<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Evolution\EvolutionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WhatsAppSettingsController extends Controller
{
    public function __construct(private EvolutionApiService $apiService) {}

    public function index()
    {
        $stateResponse = $this->apiService->checkConnectionState();
        $status = $stateResponse['instance']['state'] ?? 'close';

        Cache::put('whatsapp_status', $status, now()->addDays(7));

        $qrCode = null;
        
        if ($status !== 'open') {
            $qrResponse = $this->apiService->getQrCode();
            $qrCode = $qrResponse['base64'] ?? null; 
        }

        return view('admin.whatsapp.index', compact('status', 'qrCode'));
    }

    public function disconnect()
    {
        $success = $this->apiService->logoutInstance();

        if ($success) {
            Cache::put('whatsapp_status', 'close', now()->addDays(7));
            return redirect()->back()->with('success', 'تم فصل حساب الواتساب بنجاح.');
        }

        return redirect()->back()->with('error', 'فشل في قطع الاتصال، يرجى المحاولة لاحقاً.');
    }
}