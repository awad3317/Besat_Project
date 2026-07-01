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


public function logoutInstance(?string $instanceApiKey = null): bool
{
    $url = "{$this->baseUrl}/instance/logout"; 

    try {
        $key = '14171417Nn';

        $headers = [
            'apikey'       => $key,
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->delete($url);

        if ($response->successful()) {
            return true;
            
        }

        // 🔴 هنا يتم تسجيل الخطأ إذا كان السيرفر هو من رفض الطلب (مثلاً 400 أو 401 أو 404)
        Log::error("================ WA_LOGOUT_API_ERROR ================");
        Log::error("URL: " . $url);
        Log::error("HTTP Status: " . $response->status());
        Log::error("Server Response: " . $response->body());
        Log::error("====================================================");
        
        return false;
    } catch (\Exception $e) {
        // 🔴 هنا يتم تسجيل الخطأ إذا حدثت مشكلة في الاتصال أو كود لارافل نفسه (مثل Timeout أو خطأ كود)
        Log::error("============== WA_LOGOUT_EXCEPTION ==============");
        Log::error("Message: " . $e->getMessage());
        Log::error("File: " . $e->getFile() . " Line: " . $e->getLine());
        Log::error("=================================================");
        
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