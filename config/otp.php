<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إلزامية معرّف الجهاز (X-Device-Id)
    |--------------------------------------------------------------------------
    | اجعلها false مؤقتاً قبل تحديث تطبيق الجوال حتى لا تتعطل النسخة الحالية.
    | بعد إضافة الهيدر X-Device-Id في تحديث التطبيق، غيّرها في .env إلى true.
    */
    'require_device_id' => filter_var(env('OTP_REQUIRE_DEVICE_ID', false), FILTER_VALIDATE_BOOLEAN),
    'device_id_header'  => env('OTP_DEVICE_ID_HEADER', 'X-Device-Id'),

    /*
    |--------------------------------------------------------------------------
    | مدة الانتظار التصاعدي (Exponential Backoff) بالثواني
    |--------------------------------------------------------------------------
    | بعد المحاولة 1 -> 60 ثانية (دقيقة)
    | بعد المحاولة 2 -> 120 ثانية (دقيقتين)
    | بعد المحاولة 3 -> 240 ثانية (4 دقائق)
    | بعد المحاولة 4 -> 480 ثانية (8 دقائق)
    | بعد المحاولة 5 -> 960 ثانية (16 دقيقة)
    | بعد المحاولة 6 فأكثر -> 1800 ثانية (30 دقيقة)
    */
    'backoff_seconds' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('OTP_BACKOFF_SECONDS', '60,120,240,480,960,1800'))
    ), fn (int $seconds) => $seconds > 0)),

    // المدة بالساعات التي يُعاد بعدها تصفير عدّاد الانتظار التصاعدي تلقائياً
    'backoff_reset_hours' => (int) env('OTP_BACKOFF_RESET_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | حدود الإرسال حسب رقم الهاتف (Phone)
    |--------------------------------------------------------------------------
    */
    'phone_max_per_hour' => (int) env('OTP_PHONE_MAX_PER_HOUR', 3),
    'phone_max_per_day'  => (int) env('OTP_PHONE_MAX_PER_DAY', 5),

    /*
    |--------------------------------------------------------------------------
    | حدود الإرسال حسب معرّف الجهاز (X-Device-Id)
    |--------------------------------------------------------------------------
    */
    'device_max_per_hour'      => (int) env('OTP_DEVICE_MAX_PER_HOUR', 5),
    'device_max_per_day'       => (int) env('OTP_DEVICE_MAX_PER_DAY', 10),
    'device_max_unique_phones' => (int) env('OTP_DEVICE_MAX_UNIQUE_PHONES', 3),

    /*
    |--------------------------------------------------------------------------
    | حدود الإرسال حسب عنوان الشبكة (IP)
    |--------------------------------------------------------------------------
    */
    'ip_max_per_hour'      => (int) env('OTP_IP_MAX_PER_HOUR', 6),
    'ip_max_per_day'       => (int) env('OTP_IP_MAX_PER_DAY', 12),
    'ip_max_unique_phones' => (int) env('OTP_IP_MAX_UNIQUE_PHONES', 4),

    // نافذة احتساب الأرقام المختلفة لكل جهاز / IP بالساعات
    'unique_phones_window_hours' => (int) env('OTP_UNIQUE_PHONES_WINDOW_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | حدود مسارات التحقق من الكود وفحص وجود الحساب
    |--------------------------------------------------------------------------
    */
    'verify_max_attempts'            => (int) env('OTP_VERIFY_MAX_ATTEMPTS', 5),
    'verify_decay_minutes'           => (int) env('OTP_VERIFY_DECAY_MINUTES', 10),
    'check_existence_max_per_minute' => (int) env('OTP_CHECK_EXISTENCE_MAX_PER_MINUTE', 10),
];
