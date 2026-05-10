<?php

namespace App\Http\Controllers\API\Auth\User;

use Exception;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class UserOtpController extends Controller
{
    public function __construct(private OtpService $otpService,private UserRepository $userRepository,private TwilioService $twilioService)
    {
        
    }

    public function resendOTP(Request $request) {
        $fields=$request->validate([
            'phone'=>['required','string','min:9','max:15',Rule::exists('users','phone')],
        ]);
        try {
            $otp=$this->otpService->generateOTP($fields['phone'],'account_creation');
            // Send the OTP via Twilio WhatsApp
            $this->twilioService->sendOTP($fields['phone'], $otp);
            // $this->HypersenderService->sendTextMessage($fields['phone'],strval($otp));
            return ApiResponseClass::sendResponse(null,'Verification code has been sent to: ' . $fields['phone']);
        } catch (Exception $e) {
            return ApiResponseClass::sendError(null,'Failed to resend OTP. ' . $e->getMessage());
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
