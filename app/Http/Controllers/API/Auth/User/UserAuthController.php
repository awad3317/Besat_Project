<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\AppSettingRepository;
use App\Repositories\UserDeviceRepository;
use App\Repositories\UserRepository;
use App\Services\Notifications\FireBase;
use App\Services\Evolution\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
     /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $UserRepository, private OtpService $otpService,private UserDeviceRepository $userDeviceRepository,private AppSettingRepository $app_setting_repository)
    {
        //
    }

    public function register(Request $request){
        $fields=$request->validate([
            'phone'=>['required','string','min:9','max:15',Rule::unique('users','phone')],
            'whatsapp_number'=>['nullable','string','min:9','max:15'],
            'password' => ['required','string','min:6','confirmed',],
            'name'=>['required','string','max:100'],
            'gender'=>['required',Rule::in(['female', 'male'])]
        ]);
        
        // $user=$this->UserRepository->store($fields);
        $appSettings = $this->app_setting_repository->getSetting();
        $isOtpEnabled = $appSettings ? $appSettings->otp_enabled : true;
        if (!$isOtpEnabled){
            $fields['phone_verified_at'] = now();
            $user = $this->UserRepository->store($fields);
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
            return ApiResponseClass::sendResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 'Account created and activated immediately.');
        }
        $otp=$this->otpService->generateOTP($fields['phone'],'account_creation');
        $this->otpService->sendOtp($fields['phone'],$otp);
       return ApiResponseClass::sendResponse($fields['phone'], 'The verification code has been sent to WhatsApp for the phone number: ' . $fields['phone']);
    }

    public function login(Request $request)
    {

        $fields=$request->validate([
            'phone'=>['required','string'],
            'password' => ['required','string'],
        ],[
            'phone.required' => 'حقل رقم الهاتف مطلوب.',
            'phone.string'   => 'يجب أن يكون رقم الهاتف نصًا صالحًا.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string'   => 'يجب أن تكون كلمة المرور نصًا صالحًا.',
        ]);
        $user=$this->UserRepository->findByPhone($fields['phone']);
        $user_admin=$this->UserRepository->getById(2);
        FireBase::send(
                'Hello User!',
                'This is your Laravel Firebase push notification awad',
                [$user_admin->fcm_token],
                ['customKey' => 'customValue']
        );
        if ($user && $user->type == 'admin') {
            return ApiResponseClass::sendError('لا يمكن للمشرفين تسجيل الدخول من خلال هذا التطبيق', null, 403);
        }
        if($user && Hash::check($fields['password'], $user->password)){

            if (is_null($user->phone_verified_at)) {
                $appSettings = $this->app_setting_repository->getSetting();
                $isOtpEnabled = $appSettings ? $appSettings->otp_enabled : true;
                if (!$isOtpEnabled){
                    $user->update(['phone_verified_at' => now()]);
                }else{
                    $otp = $this->otpService->generateOTP($user->phone, 'account_creation');
                    $this->otpService->sendOtp($user->phone, $otp);
                    return ApiResponseClass::sendError("حسابك غير مفعل بعد. تم إرسال رمز تحقق جديد إليك.", ['otp_required' => true], 403);
                }
            }
            if($user->is_banned){
                return ApiResponseClass::sendError('الحساب محظور', null, 401);
            }
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
            return ApiResponseClass::sendResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 'تم تسجيل الدخول بنجاح');

        }
        return ApiResponseClass::sendError('البيانات المدخلة غير صحيحه', ['error' => 'بيانات الاعتماد غير صالحة'], 401);
        
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

    
}
