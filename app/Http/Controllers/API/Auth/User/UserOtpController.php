<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Repositories\UserRepository;
use App\Services\Evolution\OTPService;
use Illuminate\Support\Facades\Cache; 
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserOtpController extends Controller
{
    public function __construct(private OtpService $otpService,private UserRepository $userRepository,private AppSettingRepository $appSettingRepository)
    {
        
    }

    public function resendOTP(Request $request) {
    $fields = $request->validate([
        'phone' => ['required', 'string', 'min:9', 'max:15', Rule::exists('users', 'phone')],
    ]);

    try {
        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });
        
        if ($appSettings && !$appSettings->otp_enabled) {
            return ApiResponseClass::sendError('خدمة التحقق معطلة حالياً، يرجى تسجيل الدخول مباشرة.', null, 400);
        }
        $otp = $this->otpService->generateOTP($fields['phone'], 'account_creation');
        $this->otpService->sendOtp($fields['phone'], $otp);
        
        return ApiResponseClass::sendResponse(null, 'Verification code has been sent to: ' . $fields['phone']);
        
    } catch (Exception $e) {
        return ApiResponseClass::sendError('Failed to resend OTP.', $e->getMessage(), 500);
    }
}

    public function verifyOtpAndLogin(Request $request) {
        $fields = $request->validate([
            'phone' => ['required', 'string', Rule::exists('users', 'phone')],
            'otp'   => ['required', 'numeric'],
        ]);

        try {
            if(!$this->otpService->verifyOTP($fields['phone'], $fields['otp'])){
                return ApiResponseClass::sendError(
                    'Invalid or expired verification code',
                    [],
                    401 
                );
            }

            $user = $this->userRepository->findByPhone($fields['phone']);

            $user->update([
                'phone_verified_at' => now()
            ]);

            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            return ApiResponseClass::sendResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ],'OTP verified successfully. You are now logged in.');

        } catch (Exception $e) {
            return ApiResponseClass::sendError('Authentication failed. Please try again.', $e->getMessage(), 500);
        }
    }
}
