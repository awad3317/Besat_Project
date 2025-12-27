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

    public function checkUserEligibility(DiscountCode $discountCode, User $user)
    {
        $userPivot = $discountCode->users()->where('user_id', $user->id)->first();
        if ($userPivot) {
            $usageLimitPerUser = $discountCode->usage_limit_per_user;
            if ($usageLimitPerUser !== null && $userPivot->pivot->usage_count >= $usageLimitPerUser) {
                return false;
            }
        }
        return true;
    }
    
}