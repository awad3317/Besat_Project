<?php

namespace App\Http\Controllers\API\Auth\Driver;

use App\Services\OtpService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Repositories\DriverRepository;

class DriverAuthController extends Controller
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DriverRepository $driverRepository, private OtpService $otpService,private ImageService $imageService)
    {
        //
    }

    public function register(Request $request){
        $fields = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'phone'           => ['required', 'string', 'min:9', 'max:15', Rule::unique('drivers', 'phone')],
            'whatsapp_number' => ['nullable', 'string', 'min:9', 'max:15'],
            'password'        => ['required', 'string', 'min:6', 'confirmed'],
            'city'            => ['nullable', 'string', 'max:100'],
            'district'        => ['required', 'string', 'max:100'], 
            'vehicle_id'      => ['required', Rule::exists('vehicles', 'id')],
            'plate_number'    => ['required', 'string', 'max:20', Rule::unique('drivers', 'plate_number')],
            'identity_number' => ['required', 'string', 'max:50', Rule::unique('drivers', 'identity_number')],
            'driver_image'    => ['nullable', Rule::image()->max(2048)], 
            'vehicle_image'   => ['nullable', Rule::image()->max(4096)],
            'identity_image'  => ['required', Rule::image()->max(4096)], 
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
        ]);
        if ($request->hasFile('driver_image')) {
            $fields['driver_image'] = $this->imageService->saveImage($request->file('driver_image'), 'drivers/avatars');
        }
        if ($request->hasFile('vehicle_image')) {
            $fields['vehicle_image'] = $this->imageService->saveImage($request->file('vehicle_image'), 'drivers/vehicles');
        }
        if ($request->hasFile('identity_image')) {
            $fields['identity_image'] = $this->imageService->saveImage($request->file('identity_image'), 'drivers/identities');
        }
        $driver = $this->driverRepository->store($fields);
        
        return ApiResponseClass::sendResponse(
            $driver,
            'تم تسجيل بياناتك بنجاح. حسابك قيد المراجعة حالياً من قبل الإدارة وسيتم تفعيله فور تدقيق البيانات.'
        );
    }

    public function login(Request $request){
        $fields = $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required'    => 'حقل رقم الهاتف مطلوب.',
            'phone.string'      => 'يجب أن يكون رقم الهاتف نصًا صالحًا.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string'   => 'يجب أن تكون كلمة المرور نصًا صالحًا.',
        ]);

        $driver = $this->driverRepository->findByPhone($fields['phone']);
        if ($driver && Hash::check($fields['password'], $driver->password)) {
            if ($driver->is_banned) {
                return ApiResponseClass::sendError('تم حظر هذا الحساب من قبل الإدارة. يرجى التواصل مع الدعم الفني.', null, 403);
            }
            if (!$driver->is_active) {
                return ApiResponseClass::sendError('حسابك قيد المراجعة حالياً، يرجى الانتظار حتى يتم تفعيله من قبل الإدارة.', null, 401);
            }
            $driver->tokens()->delete();
            $token = $driver->createToken($driver->name . '-AuthToken')->plainTextToken;
            return ApiResponseClass::sendResponse([
                'driver' => $driver,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 'تم تسجيل الدخول بنجاح');
        }
        return ApiResponseClass::sendError('البيانات المدخلة غير صحيحة', ['error' => 'بيانات الاعتماد غير صالحة'], 401);
    }

    public function logout(Request $request)
    {
        $driver = auth('sanctum')->user();
        if ($driver){
            $driver->device_token = null;
            $driver->is_online = false;
            $driver->save();
            $driver->tokens()->delete();
            return ApiResponseClass::sendResponse(null, 'تم تسجيل الخروج بنجاح');
        }
        
        return ApiResponseClass::sendError('لم يتم العثور على جلسة نشطة', null, 401);
    }

}
