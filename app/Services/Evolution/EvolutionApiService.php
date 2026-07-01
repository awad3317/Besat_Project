<?php

namespace App\Services\Evolution;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $defaultInstance;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('EVOLUTION_API_BASE_URL'), '/');
        $this->apiKey = env('EVOLUTION_API_KEY');
        $this->defaultInstance = env('EVOLUTION_API_DEFAULT_INSTANCE');
    }

    

    private function getHeaders(): array
    {
        return [
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function checkConnectionState(?string $instanceName = null): array
    {
        $instance = $instanceName ?? $this->defaultInstance;
        $url = "{$this->baseUrl}/instance/status";

       $response = Http::withHeaders($this->getHeaders())->get($url, [
            'instance' => $instance
        ]);
        if ($response->successful() && !is_null($response->json())) {
           $resData = $response->json();
            
            $isConnected = isset($resData['data']['Connected']) && $resData['data']['Connected'] === true;
            $isLoggedIn = isset($resData['data']['LoggedIn']) && $resData['data']['LoggedIn'] === true;
            $state = ($isConnected && $isLoggedIn) ? 'open' : 'close';
            return ['instance' => ['state' => $state]];
        }
        Log::error("WhatsApp Instance Status Error: " . $response->body());
        return ['instance' => ['state' => 'close']];
    }

   public function getQrCode(?string $instanceName = null): array|bool
{
    $instance = $instanceName ?? $this->defaultInstance;
    $url = "{$this->baseUrl}/instance/qr";

    try {
        $response = Http::withHeaders($this->getHeaders())->get($url, [
            'instance' => $instance
        ]);

        if ($response->successful() && !is_null($response->json())) {
            $resData = $response->json();
            
            // قراءة الحقل بدقة كما ظهر في الـ dd(): data.Qrcode
            $base64 = $resData['data']['Qrcode'] ?? null;
            $code = $resData['data']['Code'] ?? null;
            
            if ($base64) {
                return [
                    'base64' => $base64,
                    'code' => $code
                ];
            }
        }

        Log::error("WhatsApp Get QR Error - Response: " . $response->body());
        return false;

    } catch (\Exception $e) {
        Log::error("WhatsApp Get QR Exception: " . $e->getMessage());
        return false;
    }
}


/**
 * عمل تسجيل خروج وفصل الرقم بإرسال الـ Instance داخل الـ Body (JSON)
 */
public function logoutInstance(?string $instanceName = null): bool
{
    $instance = $instanceName ?? $this->defaultInstance;
    $url = "{$this->baseUrl}/instance/disconnect";

    try {
        // تمرير البيانات مباشرة داخل الـ post لترسل كـ JSON Body طبقاً للتوثيق في الصورة
        $response = Http::withHeaders($this->getHeaders())->post($url, [
            'instance' => $instance
        ]);
        if ($response->successful()) {
            return true;
        }

        Log::error("WhatsApp Disconnect/Logout Error - Response: " . $response->body());
        return false;
    } catch (\Exception $e) {
        Log::error("WhatsApp Disconnect/Logout Exception: " . $e->getMessage());
        return false;
    }
}

    
    public function sendText(string $number, string $text, ?string $instanceName = null): array|bool
{
    $instance = $instanceName ?? $this->defaultInstance;
    $url = "{$this->baseUrl}/send/text";

    $randomDelay = rand(1500, 2000);

    try {
        $response = Http::withHeaders($this->getHeaders())->post($url, [
            'number' => $number,
            'text' => $text,
            'delay' => $randomDelay,
            'options' => [
                'presence' => 'composing', // إظهار حالة "يكتب..." للمستلم
            ]
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error("WhatsApp SendText Error: " . $response->body());
        return false;

    } catch (\Exception $e) {
        Log::error("WhatsApp SendText Exception: " . $e->getMessage());
        return false;
    }
}

    
    public function checkNumberExists(string $number, ?string $instanceName = null): bool
    {
        $instance = $instanceName ?? $this->defaultInstance;
        $url = "{$this->baseUrl}/user/check";

        $response = Http::withHeaders($this->getHeaders())->post($url, [
            'number' => [$number]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return isset($data['data']['Users'][0]['IsInWhatsapp']) && 
                       $data['data']['Users'][0]['IsInWhatsapp'] === true;
        }

        return false;
    }
}