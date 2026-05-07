<?php

namespace App\Services\Evolution;

use App\Repositories\OtpRepository;
use App\Services\Evolution\EvolutionApiService;
use Illuminate\Support\Facades\Log;

class OTPService
{
    public function __construct(private EvolutionApiService $evolutionApiService,private OtpRepository $OtpRepository)
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
        $message = "مرحباً بك في تطبيق بساط \n";
        $message .= "كود التحقق الخاص بك هو: *{$otp}*\n";
        $message .= "صالح لمدة 10 دقائق. لا تشارك هذا الكود مع أحد.";

        try {
            $response = $this->evolutionApiService->sendText($phone,$message);
            return $response !== false;
        } catch (\Exception $e) {
            Log::error('WhatsApp OTP Error: ' . $e->getMessage());
            return false;
        }
    }
}