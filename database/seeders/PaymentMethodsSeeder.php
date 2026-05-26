<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\Bank;

class PaymentMethodsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء العملات الأساسية
        $yer = Currency::firstOrCreate(['code' => 'YER'], ['name' => 'ريال يمني']);
        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'دولار أمريكي']);
        $sar = Currency::firstOrCreate(['code' => 'SAR'], ['name' => 'ريال سعودي']);

        // 2. إنشاء بوابة بنك القطيبي
        $qutaibi = Bank::create([
            'method_key' => 'qutaibi_e_payment',
            'name' => 'بنك القطيبي الإسلامي',
            'logo' => 'https://safirapps.com/storage/digital-methods/qutaibi.webp',
            'color' => '#AEC737',
            'is_active' => true,
        ]);

        // ربط البنك بالعملات الثلاث
        $qutaibi->currencies()->attach([$yer->id, $usd->id, $sar->id]);

        // 3. إنشاء الخطوة الأولى للقطيبي (طلب الـ OTP)
        $step1 = $qutaibi->steps()->create([
            'step_key' => 'request_otp',
            'action_text' => 'التالي',
            'action_url' => '/api/v2/customer/payment/request_otp/qutaibi_e_payment',
            'sort_order' => 1
        ]);

        // إضافة حقول الخطوة الأولى
        $step1->fields()->createMany([
            [
                'field_key' => 'customer_number',
                'label' => 'رقم الحساب',
                'hint' => '123456789',
                'description' => 'رقم الحساب هو رقم حسابك البنكي الذي يظهر في الشاشة الرئيسية',
                'type' => 'numeric',
                'min_length' => 6,
                'max_length' => 10,
                'is_hidden' => false,
                'default_value' => '97672521'
            ],
            [
                'field_key' => 'purchase_code',
                'label' => 'كود الشراء',
                'hint' => '123456',
                'description' => 'كود الشراء من تطبيق بنك القطيبي',
                'type' => 'numeric',
                'min_length' => 4,
                'max_length' => 8,
                'is_hidden' => true,
                'default_value' => ''
            ]
        ]);

        // 4. إنشاء الخطوة الثانية للقطيبي (إكمال الدفع)
        $step2 = $qutaibi->steps()->create([
            'step_key' => 'submit',
            'action_text' => 'إكمال الدفع',
            'action_url' => '/api/v2/customer/payment/submit/qutaibi_e_payment',
            'sort_order' => 2
        ]);

        $step2->fields()->create([
            'field_key' => 'otp',
            'label' => 'رمز التحقق',
            'hint' => '123456',
            'description' => 'ستصلك رسالة واتساب برمز التحقق',
            'type' => 'numeric',
            'min_length' => 4,
            'max_length' => 8,
            'is_hidden' => false
        ]);
        
        // (يمكنك بنفس الطريقة كتابة كود عدن كاش هنا)
    }
}