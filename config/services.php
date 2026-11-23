<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'groq' => [
        'key' => env('GROQ_API_KEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    
    'firebase' => [
        'credentials' => storage_path(env('FIREBASE_CREDENTIALS')),
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
    ],

    'google' => [
        'maps_key' => env('GOOGLE_MAP_KEY'),
    ],
    
    'qutaibi' => [
        'base_url' => env('ALQUTAIBIBANK_BASE_URL', 'https://newdc.qtb-bank.com:5052/BillPayLive'),
        'api_key' => env('ALQUTAIBIBANK_API_KEY', 'xnxhSBh1KXXgHiMa'),
        'app_key' => env('ALQUTAIBIBANK_APP_KEY', '0123335Uzi4RTestUmnuuumffUp'),
        'destination' => (int) env('ALQUTAIBIBANK_PAYMENT_DESTINATION', 11250875),
        'encrypted_customer_no' => env('ALQUTAIBIBANK_CUSTOMER_NO', 'Batwy3YPl8D2ZVoucGQmkw=='),
    ],

    'evolution' => [
        'base_url' => env('EVOLUTION_API_BASE_URL'),
        'api_key' => env('EVOLUTION_API_KEY'),
        'default_instance' => env('EVOLUTION_API_DEFAULT_INSTANCE'),
    ],
    
    'whatsapp_otp' => [
        'driver' => env('WHATSAPP_OTP_DRIVER', 'evolution'),
        'fallback_to_evolution' => filter_var(env('WHATSAPP_OTP_FALLBACK_TO_EVOLUTION', true), FILTER_VALIDATE_BOOLEAN),
        'meta_test_numbers' => array_values(array_filter(
            array_map('trim', explode(',', (string) env('WHATSAPP_META_TEST_NUMBERS', '')))
        )),
        'meta' => [
            'token' => env('WHATSAPP_META_TOKEN'),
            'phone_id' => env('WHATSAPP_META_PHONE_ID'),
            'version' => env('WHATSAPP_META_API_VERSION', 'v21.0'),
            'template_name' => env('WHATSAPP_META_TEMPLATE_NAME', 'busat_otp_autofill'),
            'template_lang' => env('WHATSAPP_META_TEMPLATE_LANG', 'ar'),
            'timeout' => (int) env('WHATSAPP_META_TIMEOUT', 6),
            'default_country_code' => env('WHATSAPP_META_DEFAULT_COUNTRY_CODE', '967'),
        ],
    ],

    'customer_service' => [
        'whatsapp' => env('CUSTOMER_SERVICE_WHATSAPP', '+967776947168'),
    ],


];
