<?php

namespace App\Livewire\Request;

use Livewire\Component;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\DiscountCode;
use Livewire\Attributes\Computed;
use App\Services\DiscountCodeService;
use App\Services\PriceCalculationService;
use Carbon\Carbon;

class Create extends Component
{
    public $coupon_code = '';
    public $coupon_message = '';
    public $customer_phone = '';
    public $customers_list = [];
    public $selected_customer_id = null;
    public $wallet_balance = 0;

    // Google Maps Properties
    public $start_lat = null;
    public $start_lng = null;
    public $start_address = '';
    public $end_lat = null;
    public $end_lng = null;
    public $end_address = '';
    public $stops = []; // Array to hold waypoints

    // New Properties
    public $vehicle_id = '';
    public $wants_ac = false;
    public $show_ac_options = false;

    public $trip_date = '';
    public $trip_time = '';

    public $payment_method = 'cash';
    public $bank_name = '';
    public $account_number = '';

    // Distance and Price Breakdown
    public $distance_km = 0;
    public $price_details = null;
    public $calculation_error = null;

    // متغييرات إنشاء مستخدم جديد
    public $new_user_name = '';
    public $new_user_phone = '';
    public $new_user_whatsapp = '';
    public $new_user_password = '';

    protected $listeners = ['updateDistance' => 'setDistanceAndCalculate'];

    public function mount()
    {
        $this->trip_date = now()->format('Y-m-d');
        $this->trip_time = now()->format('H:i');
    }

    public function updatedCustomerPhone($value)
    {
        if (strlen($value) >= 1) {
            $this->customers_list = User::where('phone', 'like', '%' . $value . '%')
                ->orWhere('name', 'like', '%' . $value . '%')
                ->where('type', 'user')
                ->where('is_banned', 0)
                ->limit(5)
                ->get();
        } else {
            $this->customers_list = [];
        }
        $this->selected_customer_id = null;
        $this->wallet_balance = 0;
    }

    public function selectCustomer($customerId, $customerPhone, $name, $walletBalance = 0)
    {
        $this->customer_phone = $customerPhone . ' - ' . $name;
        $this->selected_customer_id = $customerId;
        $this->wallet_balance = $walletBalance;
        $this->customers_list = [];
        $this->calculatePrice();
    }

    public function resetCustomer()
    {
        $this->customer_phone = '';
        $this->selected_customer_id = null;
        $this->wallet_balance = 0;
        $this->customers_list = [];
        $this->calculatePrice();
    }

    public function applyCoupon(DiscountCodeService $discountCodeService)
    {
        $this->reset(['coupon_message', 'calculation_error']);

        if (empty($this->coupon_code)) {
            $this->calculatePrice();
            return;
        }

        $coupon = $discountCodeService->getDiscountCode($this->coupon_code);

        if (!$coupon) {
            $this->coupon_message = 'كود الخصم غير صحيح.';
            $this->calculatePrice();
            return;
        }
        if (!$discountCodeService->checkIsActive($coupon)) {
            $this->coupon_message = 'هذا الكود غير نشط حالياً.';
            $this->calculatePrice();
            return;
        }
        if (!$discountCodeService->checkGlobalUsage($coupon)) {
            $this->coupon_message = 'لقد تم استنفاذ الحد الأقصى لاستخدام هذا الكود.';
            $this->calculatePrice();
            return;
        }
        if ($this->selected_customer_id && !$discountCodeService->checkUserEligibility($coupon, $this->selected_customer_id)) {
            $this->coupon_message = 'كود الخصم تم استخدامه من قبل هذا المستخدم.';
            $this->calculatePrice();
            return;
        }

        $this->coupon_message = 'الكوبون صالح الاستخدام ' . number_format($coupon->discount_rate * 100, 2) . "%";
        $this->calculatePrice();
    }

    public function updatedVehicleId()
    {
        if ($this->vehicle_id) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle && $vehicle->has_ac_option) {
                $this->show_ac_options = true;
            } else {
                $this->show_ac_options = false;
                $this->wants_ac = false;
            }
        } else {
            $this->show_ac_options = false;
            $this->wants_ac = false;
        }
        $this->calculatePrice();
    }

    public function updatedWantsAc()
    {
        $this->calculatePrice();
    }

    public function updatedTripDate()
    {
        $this->calculatePrice();
    }

    public function updatedTripTime()
    {
        $this->calculatePrice();
    }

    public function setDistanceAndCalculate($distanceInMeters)
    {
        $this->distance_km = $distanceInMeters / 1000;
        $this->calculatePrice();
    }

    public function calculatePrice()
    {
        $this->price_details = null;
        $this->calculation_error = null;

        if (!$this->vehicle_id || $this->distance_km <= 0) {
            return;
        }

        try {
            $priceService = app(PriceCalculationService::class);
            $discountService = app(DiscountCodeService::class);

            $vehicle = Vehicle::find($this->vehicle_id);
            if (!$vehicle) return;

            $price_per_km = $priceService->getPricePerKmByDistanceAndVehicle($this->distance_km, $vehicle);
            $base_price = $priceService->calculatePrice($this->distance_km, $price_per_km, $vehicle->min_price);

            $surcharges_details = [];
            $ac_cost = 0;

            if ($this->wants_ac && $vehicle->has_ac_option) {
                $ac_cost = $this->distance_km * $vehicle->ac_price_per_km;
                $ac_cost = round((float) $ac_cost, 2);
                $surcharges_details[] = [
                    'id'     => 'ac_cost',
                    'name'   => 'رسوم تشغيل التكييف',
                    'amount' => $ac_cost
                ];
            }

            $tripDatetime = Carbon::parse($this->trip_date . ' ' . $this->trip_time)->format('Y-m-d H:i:s');
            $surchargesData = $priceService->calculateSurcharges($tripDatetime);
            $total_surcharge_amount = $surchargesData['total_amount'];

            if (!empty($surchargesData['details'])) {
                $surcharges_details = array_merge($surcharges_details, $surchargesData['details']);
            }

            $original_price = $base_price + $ac_cost + $total_surcharge_amount;
            $final_price = $original_price;
            $discount_amount = 0;

            if ($this->coupon_code && strpos($this->coupon_message, 'صالح') !== false) {
                $coupon = $discountService->getDiscountCode($this->coupon_code);
                if ($coupon) {
                    $discount_amount = round($original_price * $coupon->discount_rate, 2);
                    $final_price -= $discount_amount;
                    $surcharges_details[] = [
                        'id'     => 'discount',
                        'name'   => 'خصم قسيمة',
                        'amount' => -$discount_amount
                    ];
                }
            }

            $this->price_details = [
                'distance_km' => round($this->distance_km, 2),
                'base_price' => round($base_price, 2),
                'ac_cost' => $ac_cost,
                'total_surcharges' => $total_surcharge_amount,
                'discount_amount' => $discount_amount,
                'final_price' => round($final_price, 2),
                'surcharges_details' => $surcharges_details,
                'price_per_km' => $price_per_km,
            ];
        } catch (\Exception $e) {
            $this->calculation_error = 'حدث خطأ أثناء حساب السعر';
        }
    }
    public function storeNewCustomer()
    {
        // التحقق من صحة البيانات
        $this->validate([
            'new_user_name' => 'required|string|max:255',
            'new_user_phone' => 'required|string|unique:users,phone',
            'new_user_whatsapp' => 'nullable|string',
            'new_user_password' => 'required|string|min:8',
        ], [
            'new_user_phone.unique' => 'رقم الجوال مسجل مسبقاً في النظام.',
            'new_user_password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
        ]);

        // إنشاء المستخدم
        $user = User::create([
            'name' => $this->new_user_name,
            'phone' => $this->new_user_phone,
            'whatsapp_number' => $this->new_user_whatsapp,
            'password' => bcrypt($this->new_user_password),
            'type' => 'user',
            'is_banned' => 0,
        ]);

        // تحديد المستخدم الجديد تلقائياً في خانة البحث
        $this->selectCustomer($user->id, $user->phone, $user->name, 0);

        // تفريغ الحقول بعد الإنشاء
        $this->reset(['new_user_name', 'new_user_phone', 'new_user_whatsapp', 'new_user_password']);

        // إرسال حدث لإغلاق النافذة المنبثقة
        $this->dispatch('close-user-modal');
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
