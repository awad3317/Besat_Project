<?php

namespace App\Livewire\SpecialOrders;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Vehicle;
use App\Services\DiscountCodeService;
use App\Models\DiscountCode;

class Create extends Component
{
    public $coupon_code = '';
    public $coupon_message = '';

    public function applyCoupon(DiscountCodeService $discountCodeService)
    {
        $this->reset(['coupon_message']);
        $coupon = $discountCodeService->getDiscountCode($this->coupon_code);
        if(!$coupon){
            $this->coupon_message = 'كود الخصم غير صحيح.';
            return;
        }
        if (!$discountCodeService->checkIsActive($coupon)) {
            $this->coupon_message = 'هذا الكود غير نشط حالياً.';
            return;
        }

        if (!$discountCodeService->checkGlobalUsage($coupon)) {
            $this->coupon_message = 'لقد تم استنفاذ الحد الأقصى لاستخدام هذا الكود.';
            return;
        }
        return $this->coupon_message= 'الكوبون صالح الاستخدام  ' . number_format($coupon->discount_rate * 100, 2) . "%";
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::get();
    }
    public function render()
    {
        return view('livewire.special-orders.create');
    }
}
