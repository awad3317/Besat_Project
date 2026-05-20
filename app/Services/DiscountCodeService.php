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
    public function validateCoupon(string $code, int $userId): array
    {
        $coupon = $this->getDiscountCode($code);

        if (!$coupon) {
            return ['is_valid' => false, 'message' => 'كود الخصم الذي أدخلته غير صحيح.', 'status_code' => 422];
        }
        if (!$this->checkIsActive($coupon)) {
            return ['is_valid' => false, 'message' => 'كود الخصم غير متاح', 'status_code' => 400];
        }   
        if (!$this->checkGlobalUsage($coupon)) {
            return ['is_valid' => false, 'message' => 'كود الخصم تجاوز الاستخدام المسموح به', 'status_code' => 400];
        }
        if (!$this->checkUserEligibility($coupon, $userId)) {
            return ['is_valid' => false, 'message' => 'كود الخصم تم استخدامه من قبل هذا المستخدم مسبقاً.', 'status_code' => 400];
        }

        return ['is_valid' => true, 'coupon' => $coupon];
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