<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
     /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $UserRepository)
    {
        //
    }

    public function updateDeviceToken(Request $request){
        $fields=$request->validate([
            'device_token' => 'required',
        ]);
        try {
            $user_id=auth('sanctum')->id();
            $user=$this->UserRepository->update($fields,$user_id);
            return ApiResponseClass::sendResponse($user,'Device token updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Error updated token.');
        }
        
    }

    public function index(){
        try {
            $userId = auth('sanctum')->id();
            $userProfile = $this->UserRepository->getById($userId);
            if (!$userProfile) {
                return ApiResponseClass::sendError('المستخدم غير موجود', [], 404);
            }
            return ApiResponseClass::sendResponse($userProfile, 'تم جلب بيانات الملف الشخصي بنجاح.');
        } catch (Exception $e) {
            Log::error('Error fetching user profile: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء جلب بيانات الملف الشخصي.', $e->getMessage(), 500);
        }
    }

    public function updateProfile(Request $request){
        $fields = $request->validate([
            'name'            => ['sometimes', 'required', 'string', 'max:255'],
            'whatsapp_number' => ['sometimes', 'nullable', 'string'],
            'gender'          => ['sometimes', 'required', Rule::in(['female', 'male'])],
            'location'        => ['sometimes', 'nullable', 'string'],
            // 'image'           => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        try {
            $userId = auth('sanctum')->id();
            // if ($request->hasFile('image')) {
            //     $fields['image'] = $request->file('image')->store('users', 'public');
            // }
            $updatedUser = $this->UserRepository->update($fields, $userId);
            return ApiResponseClass::sendResponse($updatedUser, 'تم تحديث بيانات الملف الشخصي بنجاح.');
        } catch (Exception $e) {
            Log::error('Error updating user profile: ' . $e->getMessage());
            return ApiResponseClass::sendError('حدث خطأ أثناء تحديث البيانات.', $e->getMessage(), 500);
        }
    }
}
