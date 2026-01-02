<?php

namespace App\Services;

use App\Models\User;
use App\Models\DiscountCode;
use App\Repositories\DiscountCodeRepository;

class DiscountCodeService{
    public function __construct(private DiscountCodeRepository $discountCodeRepository)
    {
        //
    }

    public function getDiscountCode(string $code)
    {
        return DiscountCode::where('code', $code)->first();
    }

    public function checkIsActive(DiscountCode $discountCode)
    {
        if (!$discountCode->is_active) {
            return false;
        }
        return true;
    }
    
    public function checkGlobalUsage(DiscountCode $discountCode)
    {
        if ($discountCode->current_uses >= $discountCode->max_uses) {
            return false;
        }
        return true;
    }

    public function checkUserEligibility(DiscountCode $discountCode,$user_id)
    {
        $userPivot = $discountCode->users()->find($user_id);
        if ($userPivot) {
            $usageLimitPerUser = $discountCode->usage_limit_per_user;
            if ($usageLimitPerUser !== null && $userPivot->pivot->usage_count >= $usageLimitPerUser) {
                return false;
            }
        }
        return true;
    }

    
/**
 * تسجل استخدام كوبون الخصم لمستخدم معين.
 * إذا كان المستخدم قد استخدم الكود من قبل، يتم زيادة العداد.
 * إذا كان هذا أول استخدام، يتم إنشاء سجل جديد في الجدول الوسيط.
 *
 * @param DiscountCode $discountCode الكوبون الذي تم استخدامه.
 * @param int $userId معرّف المستخدم.
 * @return void
 */
public function recordCouponUsage(DiscountCode $discountCode, int $userId): void
{
    $discountCode->current_uses = $discountCode->current_uses + 1;
    $discountCode->save();
    $userOnPivot = $discountCode->users()->find($userId);
    if ($userOnPivot) {
        $newUsageCount = $userOnPivot->pivot->usage_count + 1;
        $discountCode->users()->updateExistingPivot($userId, [
            'usage_count' => $newUsageCount,
        ]);
    } else {
        $discountCode->users()->attach($userId, ['usage_count' => 1]);
    }}

}