<?php

namespace App\Services\Evolution;

use App\Repositories\OtpRepository;
use App\Services\Evolution\EvolutionApiService;
use App\Services\WhatsApp\MetaCloudOtpService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OTPService
{
    public function __construct(private EvolutionApiService $evolutionApiService,private OtpRepository $OtpRepository,private MetaCloudOtpService $metaCloudOtpService)
    {
        
    }
    public function generateOTP($phone,$purpose)
    {
        $existingOtp=$this->OtpRepository->findByPhone($phone);
        if($existingOtp){
            $this->OtpRepository->delete($existingOtp->id);
        }
        $otp = rand(100000, 999999); 
        $expiresAt = now()->addMinutes(10); 
        $data=[
            'phone' => $phone,
            'code' => $otp,
            'expires_at' => $expiresAt,
            'purpose' => $purpose,
        ];
        $this->OtpRepository->store($data);
        return $otp; 
    }

    public function verifyOTP($phone, $code)
    {
        $otp = $this->OtpRepository->verifyOTP($phone, $code);

        if ($otp) {
            $otp->is_used = true;
            $otp->save();
            return true;
        }

        return false; 
    }

    public function hasWhatsApp(string $phone): bool
    {
        return $this->evolutionApiService->checkNumberExists($phone);
    }
 

    public function sendOtp($phone, string $otp): bool
    {
        $phoneStr = (string) $phone;
        if (! $this->shouldUseMeta($phoneStr)) {
            return $this->sendViaEvolution($phoneStr, $otp);
        }

        
        try {
            $this->metaCloudOtpService->sendOtp($phoneStr, $otp);
            return true;
        } catch (\Throwable $e) {
            
            Log::error('WhatsApp Meta OTP failed', [
                'phone' => $phoneStr,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            if (! config('services.whatsapp_otp.fallback_to_evolution', true)) {
                return false;
            }

            $fallbackSent = $this->sendViaEvolution($phoneStr, $otp);

            if ($fallbackSent) {
                Log::warning('WhatsApp OTP delivered via Evolution Go fallback', [
                    'phone' => $phoneStr,
                ]);
                return true;
            }

            Log::critical('Both Meta and Evolution Go OTP providers failed', [
                'phone' => $phoneStr,
                'meta_error' => $e->getMessage(),
            ]);

            return false;
        }
    }
    private function shouldUseMeta(string $phone): bool
    {
        if (config('services.whatsapp_otp.driver', 'evolution') === 'meta') {
            return true;
        }

        $testNumbers = config('services.whatsapp_otp.meta_test_numbers', []);
        if (empty($testNumbers)) {
            return false;
        }

        $normalizedTarget = $this->metaCloudOtpService->normalizePhone($phone);

        foreach ($testNumbers as $testNumber) {
            if ($this->metaCloudOtpService->normalizePhone($testNumber) === $normalizedTarget) {
                return true;
            }
        }

        return false;
    }

    private function sendViaEvolution(string $phone, string $otp): bool
    {
        $greetings = ['مرحباً بك', 'أهلاً بك', 'تحية طيبة', 'أهلاً بك عزيزي العميل', 'يا هلا بك'];
        $systemNames = ['في تطبيق بساط', 'عبر منصة بساط', 'في بساط', 'مع خدمات بساط'];
        $actions = ['كود التحقق الخاص بك هو:', 'رمز الدخول الخاص بك:', 'كود التفعيل هو:', 'الرمز السري الخاص بك:'];
        $warnings = ['لا تشارك هذا الكود مع أحد.', 'يرجى الحفاظ على سرية الكود.', 'هذا الكود سري للغاية.', 'تأكد من عدم مشاركة الرمز.'];

        $greeting = $greetings[array_rand($greetings)];
        $systemName = $systemNames[array_rand($systemNames)];
        $action = $actions[array_rand($actions)];
        $warning = $warnings[array_rand($warnings)];

        $ref = Str::random(5);
        $time = now()->format('H:i:s');

        $message = "{$greeting} {$systemName} 🛡️\n\n";
        $message .= "{$action} *{$otp}*\n";
        $message .= "⏳ صالح لمدة 10 دقائق. {$warning}\n\n";
        $message .= "💡 للمساعدة، يمكنك الرد على هذه الرسالة.\n";
        $message .= "`[Ref: {$ref}-{$time}]`";

        try {
            $response = $this->evolutionApiService->sendText($phone, $message);
            return $response !== false;
        } catch (\Exception $e) {
            Log::error('WhatsApp Evolution OTP Error: ' . $e->getMessage());
            return false;
        }
    }
}