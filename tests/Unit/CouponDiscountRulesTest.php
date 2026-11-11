<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DiscountCode;
use App\Services\DiscountCodeService;
use App\Services\PriceCalculationService;
use App\Repositories\DiscountCodeRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\AppSettingRepository;
use App\Models\app_setting;

class CouponDiscountRulesTest extends TestCase
{
    private DiscountCodeService $discountService;
    private PriceCalculationService $priceService;
    private DiscountCode $cappedCoupon;
    private DiscountCode $openCoupon;

    protected function setUp(): void
    {
        parent::setUp();

        $discountRepo = $this->createMock(DiscountCodeRepository::class);
        $this->discountService = new DiscountCodeService($discountRepo);

        $vehicleRepo = $this->createMock(VehicleRepository::class);
        $settingRepo = $this->createMock(AppSettingRepository::class);

        // ضبط نسبة العمولة على 15% كما في سيناريو الاختبار
        $fakeSetting = new app_setting(['commission_rate' => 15]);
        $settingRepo->method('getSetting')->willReturn($fakeSetting);

        $this->priceService = new PriceCalculationService($vehicleRepo, $settingRepo);

        // كوبون محدد: نسبة 20%، سقف 1500 ريال، حد أدنى 3000 ريال
        $this->cappedCoupon = new DiscountCode([
            'code'                => 'SAVE20',
            'discount_rate'       => 0.20,
            'max_discount_amount' => 1500.00,
            'min_trip_fare'       => 3000.00,
            'is_active'           => true,
            'max_uses'            => 100,
            'current_uses'        => 0,
        ]);

        // كوبون مفتوح: نسبة 20%، بدون سقف، بدون حد أدنى
        $this->openCoupon = new DiscountCode([
            'code'                => 'OPEN20',
            'discount_rate'       => 0.20,
            'max_discount_amount' => null,
            'min_trip_fare'       => null,
            'is_active'           => true,
            'max_uses'            => 100,
            'current_uses'        => 0,
        ]);
    }

    /**
     * الحالة 1: رحلة بسعر 2,500 ريال -> يُرفض الكوبون لأنه أقل من الحد الأدنى (3,000 ريال).
     */
    public function test_case_1_reject_coupon_when_fare_below_minimum()
    {
        $fare = 2500.0;

        // التحقق من فحص الحد الأدنى في الخدمة
        $isValidFare = $this->discountService->checkMinTripFare($this->cappedCoupon, $fare);
        $this->assertFalse($isValidFare, 'يجب رفض الكوبون لأن السعر أقل من 3000 ريال');

        // في حال تم استدعاء حساب الخصم بسعر أقل من الحد الأدنى
        $couponRate = (float) $this->cappedCoupon->discount_rate;
        $minFare = (float) $this->cappedCoupon->min_trip_fare;

        if ($fare < $minFare) {
            $discount = 0;
        } else {
            $discount = min($this->priceService->roundPrice($fare * $couponRate), (float) $this->cappedCoupon->max_discount_amount);
        }

        $this->assertEquals(0, $discount, 'الخصم الفعلي يجب أن يكون 0 ريال عند عدم استيفاء الحد الأدنى');
    }

    /**
     * الحالة 2: رحلة قصيرة بسعر 4,000 ريال -> الخصم الفعلي 800 ريال، المطلوب من العميل 3,200 ريال، عمولة التطبيق 600 ريال.
     */
    public function test_case_2_short_trip_fare_4000()
    {
        $originalFare = 4000;
        $this->assertTrue($this->discountService->checkMinTripFare($this->cappedCoupon, $originalFare));

        $couponRate = (float) $this->cappedCoupon->discount_rate;
        $roundedCalculatedDiscount = $this->priceService->roundPrice($originalFare * $couponRate);
        $finalDiscount = (int) min($roundedCalculatedDiscount, (float) $this->cappedCoupon->max_discount_amount);
        $customerPayable = $originalFare - $finalDiscount;
        $commission = $this->priceService->calculateCommission($originalFare);
        $driverNet = $originalFare - $commission;
        $companyNet = $commission - $finalDiscount;

        $this->assertEquals(800, $finalDiscount, 'الخصم الفعلي يجب أن يكون 800 ريال');
        $this->assertEquals(3200, $customerPayable, 'المطلوب من العميل يجب أن يكون 3200 ريال');
        $this->assertEquals(600, $commission, 'عمولة التطبيق (15%) يجب أن تكون 600 ريال');
        $this->assertEquals(3400, $driverNet, 'صافي السائق يجب أن يكون 3400 ريال دون أن ينقص');
        $this->assertEquals(-200, $companyNet, 'صافي دخل الشركة');
    }

    /**
     * الحالة 3: رحلة متوسطة بسعر 6,500 ريال -> الخصم الفعلي 1,300 ريال، المطلوب من العميل 5,200 ريال، عمولة التطبيق 975 ريال.
     */
    public function test_case_3_medium_trip_fare_6500()
    {
        $originalFare = 6500;
        $this->assertTrue($this->discountService->checkMinTripFare($this->cappedCoupon, $originalFare));

        $couponRate = (float) $this->cappedCoupon->discount_rate;
        $roundedCalculatedDiscount = $this->priceService->roundPrice($originalFare * $couponRate);
        $finalDiscount = (int) min($roundedCalculatedDiscount, (float) $this->cappedCoupon->max_discount_amount);
        $customerPayable = $originalFare - $finalDiscount;
        $commission = $this->priceService->calculateCommission($originalFare);
        $driverNet = $originalFare - $commission;

        $this->assertEquals(1300, $finalDiscount, 'الخصم الفعلي يجب أن يكون 1300 ريال');
        $this->assertEquals(5200, $customerPayable, 'المطلوب من العميل يجب أن يكون 5200 ريال');
        $this->assertEquals(975, $commission, 'عمولة التطبيق (15%) يجب أن تكون 975 ريال');
        $this->assertEquals(5525, $driverNet, 'صافي السائق يجب أن يكون 5525 ريال');
    }

    /**
     * الحالة 4: رحلة فوق المتوسطة بسعر 10,000 ريال -> النسبة 2000 ولكن الخصم يثبت عند السقف 1,500 ريال، المطلوب 8,500 ريال، عمولة التطبيق 1,500 ريال (نقطة تعادل للشركة 0 ريال).
     */
    public function test_case_4_trip_fare_10000_hits_cap()
    {
        $originalFare = 10000;
        $this->assertTrue($this->discountService->checkMinTripFare($this->cappedCoupon, $originalFare));

        $couponRate = (float) $this->cappedCoupon->discount_rate;
        $roundedCalculatedDiscount = $this->priceService->roundPrice($originalFare * $couponRate); // 2000
        $finalDiscount = (int) min($roundedCalculatedDiscount, (float) $this->cappedCoupon->max_discount_amount); // 1500
        $customerPayable = $originalFare - $finalDiscount;
        $commission = $this->priceService->calculateCommission($originalFare); // 1500
        $companyNet = $commission - $finalDiscount; // 0

        $this->assertEquals(1500, $finalDiscount, 'الخصم يجب أن يثبت عند السقف 1500 ريال');
        $this->assertEquals(8500, $customerPayable, 'المطلوب من العميل يجب أن يكون 8500 ريال');
        $this->assertEquals(1500, $commission, 'عمولة التطبيق يجب أن تكون 1500 ريال');
        $this->assertEquals(0, $companyNet, 'نقطة التعادل للشركة يجب أن تكون 0 ريال');
    }

    /**
     * الحالة 5: رحلة طويلة بسعر 20,000 ريال -> النسبة 4000 ولكن الخصم يثبت عند السقف 1,500 ريال، المطلوب 18,500 ريال، عمولة التطبيق 3,000 ريال (ربح صافي للشركة +1,500 ريال).
     */
    public function test_case_5_long_trip_fare_20000_hits_cap()
    {
        $originalFare = 20000;
        $this->assertTrue($this->discountService->checkMinTripFare($this->cappedCoupon, $originalFare));

        $couponRate = (float) $this->cappedCoupon->discount_rate;
        $roundedCalculatedDiscount = $this->priceService->roundPrice($originalFare * $couponRate); // 4000
        $finalDiscount = (int) min($roundedCalculatedDiscount, (float) $this->cappedCoupon->max_discount_amount); // 1500
        $customerPayable = $originalFare - $finalDiscount; // 18500
        $commission = $this->priceService->calculateCommission($originalFare); // 3000
        $driverNet = $originalFare - $commission; // 17000
        $companyNet = $commission - $finalDiscount; // +1500

        $this->assertEquals(1500, $finalDiscount, 'الخصم يجب أن يثبت عند السقف 1500 ريال');
        $this->assertEquals(18500, $customerPayable, 'المطلوب من العميل 18500 ريال');
        $this->assertEquals(3000, $commission, 'عمولة التطبيق 3000 ريال');
        $this->assertEquals(17000, $driverNet, 'صافي السائق 17000 ريال');
        $this->assertEquals(1500, $companyNet, 'ربح صافي للشركة +1500 ريال');
    }

    /**
     * الحالة 6: كوبون بدون سقف (max_discount_amount = NULL) -> يعمل بالنسبة المفتوحة كما كان في السابق.
     */
    public function test_case_6_open_coupon_without_cap()
    {
        $originalFare = 20000;
        $this->assertTrue($this->discountService->checkMinTripFare($this->openCoupon, $originalFare));

        $couponRate = (float) $this->openCoupon->discount_rate;
        $roundedCalculatedDiscount = $this->priceService->roundPrice($originalFare * $couponRate); // 4000

        if (!is_null($this->openCoupon->max_discount_amount) && (float) $this->openCoupon->max_discount_amount > 0) {
            $finalDiscount = (int) min($roundedCalculatedDiscount, (float) $this->openCoupon->max_discount_amount);
        } else {
            $finalDiscount = (int) $roundedCalculatedDiscount;
        }

        $customerPayable = $originalFare - $finalDiscount;

        $this->assertEquals(4000, $finalDiscount, 'الخصم المفتوح 20% من 20000 يجب أن يكون 4000 ريال');
        $this->assertEquals(16000, $customerPayable, 'المطلوب من العميل يجب أن يكون 16000 ريال');
    }
}

