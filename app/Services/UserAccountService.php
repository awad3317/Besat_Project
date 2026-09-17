<?php

namespace App\Services;

use App\Models\User;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Exception;

class UserAccountService
{
    public function __construct(private ImageService $imageService)
    {
    }

    /**
     * حذف حساب المستخدم وإخفاء بياناته الشخصية بما يتوافق مع سياسات المتاجر.
     */
    public function deleteAccount(User $user): bool
    {
        $hasActiveTrips = $user->requests()
            ->whereIn('status', ['searching_driver', 'accepted', 'on_trip', 'in_progress', 'pending'])
            ->exists();

        if ($hasActiveTrips) {
            throw new Exception('لا يمكنك حذف الحساب ولديك رحلة نشطة حالياً. يرجى الانتظار حتى اكتمالها أو إلغائها.', 400);
        }
        return DB::transaction(function () use ($user) {
            if ($user->image) {
                $this->imageService->deleteImage($user->image);
            }
            DB::transaction(function () use ($user) {
                $tokenIds = $user->tokens()->pluck('id');
                DB::table('user_devices')->whereIn('token_id', $tokenIds)->delete();
                $user->tokens()->delete();
                $user->delete();
            });
            $user->favoritePlaces()->delete();
            $timestamp = now()->timestamp;
            $user->update([
                'name'                     => 'مستخدم محذوف',
                'phone'                    => $user->phone . '_deleted_' . $timestamp,
                'whatsapp_number'          => null,
                'fcm_token'                => null,
                'image'                    => null,
                'location'                 => null,
                'is_banned'                => true, 
                'is_notifications_enabled' => false,
            ]);

            $user->delete();
            return true;
        });
    }
}