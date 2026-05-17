<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Repositories\UserDeviceRepository;
use App\Repositories\UserRepository;
use App\Services\Evolution\OTPService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
     /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $UserRepository, private OtpService $otpService,private UserDeviceRepository $userDeviceRepository,private AppSettingRepository $appSettingRepository)
    {
        //
    }

    public function register(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'phone'           => ['required', 'string', 'min:9', 'max:15', Rule::unique('users', 'phone')],
            'whatsapp_number' => ['nullable', 'string', 'min:9', 'max:15'],
            'name'            => ['required', 'string', 'max:100'],
            'gender'          => ['required', Rule::in(['female', 'male'])]
        ]);
        if ($validator->fails()) {
            return ApiResponseClass::sendValidationError('فشل التحقق من البيانات', $validator->errors(), 422);
        }
        $fields = $validator->validated();
        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });
        $isOtpEnabled = $appSettings ? $appSettings->otp_enabled : true;
        if (!$isOtpEnabled) {
            $fields['phone_verified_at'] = now();
        }
        $user = $this->UserRepository->store($fields);
        if (!$isOtpEnabled) {
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
            return ApiResponseClass::sendResponse([
                'user'         => $user,
                'token'        => $token,
                'token_type'   => 'Bearer',
                'otp_required' => false
            ], 'تم إنشاء الحساب وتفعيله مباشرة لعدم تفعيل نظام التحقق.');
        }
        $otp = $this->otpService->generateOTP($fields['phone'], 'account_creation');
        $this->otpService->sendOtp($fields['phone'], $otp);
        return ApiResponseClass::sendResponse([
            'phone'        => $fields['phone'],
            'otp_required' => true 
        ], 'تم إرسال رمز التحقق إلى الواتساب للرقم: ' . $fields['phone']);

    } catch (Exception $e) {
        Log::error('Error in user registration: ' . $e->getMessage());
        return ApiResponseClass::sendError('حدث خطأ أثناء إنشاء الحساب. يرجى المحاولة لاحقاً.', $e->getMessage(), 500);
    }
}


    public function login(Request $request)
    {
        try {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string'],
        ], [
            'phone.required' => 'حقل رقم الهاتف مطلوب.',
            'phone.string'   => 'يجب أن يكون رقم الهاتف نصًا صالحًا.',
        ]);

        if ($validator->fails()) {
            return ApiResponseClass::sendValidationError('فشل التحقق من البيانات', $validator->errors(), 422);
        }
        $fields = $validator->validated();

        $appSettings = Cache::rememberForever('app_settings', function () {
            return $this->appSettingRepository->getSetting();
        });
        
        $isOtpEnabled = $appSettings ? $appSettings->otp_enabled : true;
        $user = $this->UserRepository->findByPhone($fields['phone']);

        if (!$user) {
            return ApiResponseClass::sendError('البيانات المدخلة غير صحيحة', 'رقم الهاتف غير مسجل لدينا', 401);
        }
        if ($user->type === 'admin') {
            return ApiResponseClass::sendError('لا يمكن للمشرفين تسجيل الدخول من خلال هذا التطبيق', null, 403);
        }
        if ($user->is_banned) {
            return ApiResponseClass::sendError('الحساب محظور', null, 401);
        }
        if ($isOtpEnabled) {
            
            $otp = $this->otpService->generateOTP($user->phone, 'login_verification');
            $this->otpService->sendOtp($user->phone, $otp);

            return ApiResponseClass::sendResponse([
                'phone' => $user->phone,
                'otp_required' => true 
            ], 'تم إرسال رمز التحقق (OTP) إلى رقمك.');

        } 
        
        if (is_null($user->phone_verified_at)) {
            $user->update(['phone_verified_at' => now()]);
        }
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return ApiResponseClass::sendResponse([
            'user'         => $user,
            'token'        => $token,
            'token_type'   => 'Bearer',
            'otp_required' => false 
        ], 'تم تسجيل الدخول بنجاح');

    } catch (Exception $e) {
        Log::error('Error in user login process: ' . $e->getMessage());
        return ApiResponseClass::sendError('حدث خطأ أثناء محاولة تسجيل الدخول. يرجى المحاولة لاحقاً.', $e->getMessage(), 500);
    }
    }

    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $this->userDeviceRepository->deleteByTokenID($currentToken->id);
            $currentToken->delete();
        }
        return ApiResponseClass::sendResponse(null, 'تم تسجيل الخروج بنجاح');
    }

   public function checkUserExistence(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => ['required', 'string', 'max:20']
            ]);
            if ($validator->fails()) {
                return ApiResponseClass::sendValidationError('فشل التحقق من البيانات', $validator->errors(), 422);
            }
            $userExists = $this->UserRepository->findByPhone($request->phone);
            if ($userExists) {
                return ApiResponseClass::sendResponse(['is_exists' => true], 'المستخدم موجود في النظام', 200);
            }
            return ApiResponseClass::sendResponse(['is_exists' => false], 'المستخدم غير موجود', 200);
        } catch (Exception $e) {
            Log::error('Error context description: ' . $e->getMessage());
            return ApiResponseClass::sendError('Failed to process the request. Please try again later.', $e->getMessage(), 500);
        }
    }

    
}
