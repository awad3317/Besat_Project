<?php

namespace App\Livewire\Request;

use Livewire\Component;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\DiscountCode;
use Livewire\Attributes\Computed;
use App\Services\DiscountCodeService;

class Create extends Component
{
    public $coupon_code = '';
    public $coupon_message = '';

    public $customer_phone = '';
    public $customers_list = [];
    public $selected_customer_id = null;

    public function updatedCustomerPhone($value)
    {
        if (strlen($value) >= 1) {
            $this->customers_list = User::where('phone', 'like', '%' . $value . '%')
                ->orWhere('name', 'like', '%' . $value . '%')
                ->where('type','user')
                ->where('is_banned',0)
                ->limit(5)
                ->get();
        } else {
            $this->customers_list = [];
        }
        $this->selected_customer_id = null;
    }


    public function selectCustomer($customerId, $customerPhone,$name)
    {
        $this->customer_phone = $customerPhone. ' - ' . $name;
        $this->selected_customer_id = $customerId;
        $this->customers_list = [];
    }

    public function resetCustomer()
    {
        $this->customer_phone = '';
        $this->selected_customer_id = null;
        $this->customers_list = [];
    }

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
        return view('livewire.request.create');
    }
}
