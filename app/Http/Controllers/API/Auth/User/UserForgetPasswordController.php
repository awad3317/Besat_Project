<?php

namespace App\Http\Controllers\API\Auth\User;

use Exception;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Repositories\UserRepository;
use App\Services\Evolution\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class UserForgetPasswordController extends Controller
{
    public function __construct(private OtpService $otpService,private UserRepository $UserRepository,private AppSettingRepository $appSettingRepository)
    {
        //
    }

    public function forgetPassword(Request $request) {
    $fields = $request->validate([
        'phone' => ['required', 'string', Rule::exists('users', 'phone')],
    ]);

    try {
        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });

        if ($appSettings && !$appSettings->otp_enabled) {
            return ApiResponseClass::sendError(
                'خدمة استعادة كلمة المرور معطلة حالياً. يرجى التواصل مع الدعم الفني عبر الواتساب للمساعدة.', 
                null, 
                400
            );
        }

        $otp = $this->otpService->generateOTP($fields['phone'], 'forgetPassword');
        
        $this->otpService->sendOtp($fields['phone'], $otp);
        
        return ApiResponseClass::sendResponse(
            ['otp_required' => true], 
            'Verification code has been sent to: ' . $fields['phone']
        );

    } catch (Exception $e) {
        return ApiResponseClass::sendError('Failed to send verification code. Please try again later.', $e->getMessage(), 500);
    }
}

    public function resetPassword(Request $request) {
    $fields = $request->validate([
        'phone'        => ['required', 'string', Rule::exists('users', 'phone')],
        'otp'          => ['required', 'numeric'],
        'password' => ['required', 'string', 'min:6', 'confirmed'], 
    ]);

    try {
        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });

        if ($appSettings && !$appSettings->otp_enabled) {
            return ApiResponseClass::sendError('خدمة استعادة كلمة المرور معطلة حالياً.', null, 400);
        }

        if (!$this->otpService->verifyOTP($fields['phone'], $fields['otp'])) {
            return ApiResponseClass::sendError('Invalid or expired verification code', [], 400);
        }

        $user = $this->UserRepository->findByPhone($fields['phone']);
        
        $this->UserRepository->update([
            'password' => $fields['new_password']
        ], $user->id);

        $user->tokens()->delete();
        return ApiResponseClass::sendResponse(null, 'Password has been updated successfully');
        
    } catch (Exception $e) { 
        return ApiResponseClass::sendError('Failed to reset password. Please try again later.', $e->getMessage(), 500);
    }
}
}
