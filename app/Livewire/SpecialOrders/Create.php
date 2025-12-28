<?php

namespace App\Livewire\SpecialOrders;

use App\Models\User;
use App\Models\Vehicle;
use Livewire\Component;
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

    /**
     * هذه الدالة (Hook) تعمل تلقائيًا عند تحديث قيمة customer_phone
     */
    public function updatedCustomerPhone($value)
    {
        // إذا كان حقل البحث يحتوي على 3 أحرف أو أكثر، ابدأ البحث
        if (strlen($value) >= 3) {
            $this->customers_list = User::where('phone', 'like', '%' . $value . '%')
                ->orWhere('name', 'like', '%' . $value . '%')
                ->where('type','user')
                ->where('is_banned',0)
                ->limit(5)
                ->get();
        } else {
            
            $this->customers_list = [];
        }

        // إعادة تعيين العميل المختار إذا بدأ المستخدم بحثاً جديداً
        $this->selected_customer_id = null;
    }

    /**
     * هذه الدالة تعمل عند اختيار عميل من القائمة
     */
    public function selectCustomer($customerId, $customerPhone)
    {
        $this->customer_phone = $customerPhone;     // املأ الحقل برقم جوال العميل
        $this->selected_customer_id = $customerId;  // احتفظ بالـ ID الخاص به
        $this->customers_list = [];                 // أخفِ قائمة النتائج
    }
    // --- نهاية الإضافات ---

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
