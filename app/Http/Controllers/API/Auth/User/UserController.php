<?php

namespace App\Http\Controllers\API\Auth\User;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
     /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $UserRepository)
    {
        //
    }

    public function updateDeviceToken(Request $request)
    {
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

    public function index()
    {
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
}
