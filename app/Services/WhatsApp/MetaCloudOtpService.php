<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaCloudOtpService
{
    public function sendOtp(string $phone, string $otp): array
    {
        $cfg = config('services.whatsapp_otp.meta');

        if (empty($cfg['token']) || empty($cfg['phone_id'])) {
            throw new RuntimeException('Meta WhatsApp credentials (token or phone_id) are missing.');
        }

        $formattedPhone = $this->normalizePhone($phone);
        $url = "https://graph.facebook.com/{$cfg['version']}/{$cfg['phone_id']}/messages";

        $response = Http::withToken($cfg['token'])
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout($cfg['timeout'])
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedPhone,
                'type' => 'template',
                'template' => [
                    'name' => $cfg['template_name'],
                    'language' => [
                        'code' => $cfg['template_lang'],
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => (string) $otp],
                            ],
                        ],
                        [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => '0',
                            'parameters' => [
                                ['type' => 'text', 'text' => (string) $otp],
                            ],
                        ],
                    ],
                ],
            ]);

        $data = $response->json();

        if ($response->failed() || empty($data['messages'][0]['id'])) {
            throw new RuntimeException(json_encode([
                'http_status' => $response->status(),
                'phone' => $formattedPhone,
                'meta_response' => $data ?? $response->body(),
            ], JSON_UNESCAPED_UNICODE));
        }

        return $data;
    }

    public function normalizePhone(string $phone): string
    {
        // إزالة أي مسافات أو علامات مثل + أو -
        $digits = preg_replace('/\D+/', '', $phone);

        // إزالة 00 من البداية إن وجدت (مثل 00967...)
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // إذا كان الرقم محلياً (مثلاً 0781152674 أو 781152674) نضيف مفتاح الدولة 967
        $countryCode = (string) config('services.whatsapp_otp.meta.default_country_code', '967');

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = $countryCode . substr($digits, 1);
        } elseif (strlen($digits) === 9 && str_starts_with($digits, '7')) {
            $digits = $countryCode . $digits;
        }

        return $digits;
    }
}