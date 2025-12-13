<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        @yield('title', 'لوحة التحكم')
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('tailadmin/build/favicon.ico') }}">
    <link href="{{ asset('tailadmin/build/style.css') }}" rel="stylesheet">
    @livewireStyles
    @yield('style')
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js"></script>

    <!-- كود Firebase المبسط (يعمل على السيرفر) -->
    <script>
        // ========== إعدادات Firebase ==========
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key', '') }}",
            authDomain: "{{ config('services.firebase.auth_domain', '') }}",
            projectId: "{{ config('services.firebase.project_id', '') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket', '') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id', '') }}",
            appId: "{{ config('services.firebase.app_id', '') }}",
            measurementId: "{{ config('services.firebase.measurement_id', '') }}"
        };

        const vapidKey = "{{ env('FIREBASE_VAPID_KEY', '') }}";
        const TOKEN_STORAGE_KEY = 'fcm_token_stored';

        // ========== دالة التحقق من تحميل Firebase ==========
        function isFirebaseLoaded() {
            if (typeof firebase === 'undefined') {
                console.error('❌ Firebase SDK غير محمل');
                return false;
            }

            if (typeof firebase.initializeApp === 'undefined') {
                console.error('❌ firebase.initializeApp غير متاح');
                return false;
            }

            if (typeof firebase.messaging === 'undefined') {
                console.error('❌ firebase.messaging غير متاح');
                return false;
            }

            console.log('✅ Firebase SDK محمل بشكل صحيح');
            return true;
        }

        // ========== تهيئة Firebase ==========
        function initializeFirebase() {
            if (!isFirebaseLoaded()) {
                // محاولة تحميل Firebase يدوياً
                loadFirebaseManually();
                return false;
            }

            try {
                // التحقق من عدم تهيئة Firebase مسبقاً
                let app;
                if (firebase.apps.length === 0) {
                    app = firebase.initializeApp(firebaseConfig);
                    console.log('✅ Firebase تم تهيئته بنجاح');
                } else {
                    app = firebase.apps[0];
                    console.log('✅ Firebase مهيأ مسبقاً');
                }

                const messaging = firebase.messaging();
                console.log('✅ Firebase Messaging جاهز');

                return { app: app, messaging: messaging };

            } catch (error) {
                console.error('❌ خطأ في تهيئة Firebase:', error);
                return null;
            }
        }

        // ========== تحميل Firebase يدوياً ==========
        function loadFirebaseManually() {
            console.log('🔄 محاولة تحميل Firebase يدوياً...');

            // إذا لم يتم تحميل Firebase، حاول تحميله
            const firebaseAppScript = document.createElement('script');
            firebaseAppScript.src = 'https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js';
            firebaseAppScript.onload = function () {
                console.log('✅ firebase-app-compat.js تم تحميله');

                const firebaseMessagingScript = document.createElement('script');
                firebaseMessagingScript.src = 'https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js';
                firebaseMessagingScript.onload = function () {
                    console.log('✅ firebase-messaging-compat.js تم تحميله');

                    // أعط فرصة للصفحة لتهيئة Firebase
                    setTimeout(function () {
                        startFirebaseProcess();
                    }, 1000);
                };
                firebaseMessagingScript.onerror = function () {
                    console.error('❌ فشل تحميل firebase-messaging-compat.js');
                };
                document.head.appendChild(firebaseMessagingScript);
            };
            firebaseAppScript.onerror = function () {
                console.error('❌ فشل تحميل firebase-app-compat.js');
            };
            document.head.appendChild(firebaseAppScript);
        }

        // ========== العملية الرئيسية ==========
        async function startFirebaseProcess() {
            console.log('🚀 بدء عملية Firebase...');

            // 1. تهيئة Firebase
            const firebaseInit = initializeFirebase();
            if (!firebaseInit) {
                console.error('❌ فشل تهيئة Firebase');
                return;
            }

            const { messaging } = firebaseInit;

            // 2. تسجيل Service Worker
            if (!('serviceWorker' in navigator)) {
                console.error('❌ Service Worker غير مدعوم');
                return;
            }

            try {
                console.log('🔄 تسجيل Service Worker...');
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                console.log('✅ Service Worker مسجل:', registration.scope);

                await navigator.serviceWorker.ready;

                // 3. التحقق من الإذن
                if (!("Notification" in window)) {
                    console.log('❌ المتصفح لا يدعم الإشعارات');
                    return;
                }

                if (Notification.permission === 'granted') {
                    console.log('✅ الإذن ممنوح');
                    await processToken(messaging, registration);
                } else if (Notification.permission === 'default') {
                    console.log('🔔 طلب إذن الإشعارات...');
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        await processToken(messaging, registration);
                    } else {
                        console.log('❌ المستخدم رفض الإذن');
                    }
                } else {
                    console.log('❌ الإذن مرفوض مسبقاً');
                }

                // 4. إعداد استقبال الإشعارات
                setupMessageListener(messaging);

            } catch (error) {
                console.error('❌ خطأ في عملية Firebase:', error);
            }
        }

        // ========== معالجة التوكن ==========
        async function processToken(messaging, registration) {
            // التحقق من التوكن المخزن
            const storedToken = localStorage.getItem(TOKEN_STORAGE_KEY);

            if (storedToken) {
                console.log('✅ التوكن مخزن مسبقاً');

                // التحقق من صحة التوكن
                const isValid = await validateToken(storedToken);
                if (isValid) {
                    console.log('✅ التوكن صالح');
                    return storedToken;
                } else {
                    console.log('🔄 التوكن غير صالح');
                    localStorage.removeItem(TOKEN_STORAGE_KEY);
                }
            }

            // الحصول على توكن جديد
            return await getNewToken(messaging, registration);
        }

        // ========== الحصول على توكن جديد ==========
        async function getNewToken(messaging, registration) {
            try {
                console.log('🔄 جاري الحصول على توكن جديد...');

                const token = await messaging.getToken({
                    vapidKey: vapidKey,
                    serviceWorkerRegistration: registration
                });

                if (token) {
                    console.log('✅ تم الحصول على توكن جديد:', token.substring(0, 20) + '...');

                    // حفظ التوكن محلياً
                    localStorage.setItem(TOKEN_STORAGE_KEY, token);

                    // إرسال التوكن للسيرفر
                    await sendTokenToServer(token);

                    return token;
                }
            } catch (error) {
                console.error('❌ خطأ في الحصول على التوكن:', error);
            }

            return null;
        }

        // ========== التحقق من صحة التوكن ==========
        async function validateToken(token) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch("{{ route('firebase.validate-token') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ token: token })
                });

                if (response.ok) {
                    const data = await response.json();
                    return data.valid === true;
                }
            } catch (error) {
                console.error('❌ خطأ في التحقق من التوكن:', error);
            }

            return false;
        }

        // ========== إرسال التوكن للسيرفر ==========
        async function sendTokenToServer(token) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch("{{ route('firebase.token') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        fcm_token: token,
                        _method: "PATCH"
                    })
                });

                if (response.ok) {
                    console.log('✅ تم إرسال التوكن للسيرفر');
                }
            } catch (error) {
                console.error('❌ خطأ في إرسال التوكن:', error);
            }
        }

        // ========== إعداد استقبال الإشعارات ==========
        let messageListenerSetup = false;
        function setupMessageListener(messaging) {
            if (messageListenerSetup) {
                console.log('ℹ️ مستمع الإشعارات مضبوط مسبقاً');
                return;
            }

            messaging.onMessage(function (payload) {
                console.log('📨 إشعار مباشر:', payload);
                const data = payload.notification || payload.data || {};

                // تأخير بسيط لضمان تحميل Alpine.js
                setTimeout(() => {
                    let data = {};

                    if (payload.notification) {
                        data = {
                            title: payload.notification.title || 'إشعار جديد',
                            message: payload.notification.body || 'لديك إشعار',
                            showButtons: payload.data?.showButtons === 'true' || false
                        };
                    } else if (payload.data) {
                        data = {
                            title: payload.data.title || 'إشعار جديد',
                            message: payload.data.body || 'لديك إشعار',
                            showButtons: payload.data.showButtons === 'true' || false
                        };
                    }

                    // الحل السحري: إرسال الحدث 3 مرات بفواصل مختلفة
                    sendEventWithRetry(data);

                }, 300);
            });

            messageListenerSetup = true;
            console.log('✅ تم إعداد مستمع الإشعارات');
        }
        function sendEventWithRetry(data) { 
            console.log('🚀 محاولة إرسال الإشعار:', data.title);

            // محاولة 1: فورية
            setTimeout(() => triggerEvent(data), 0);

            // محاولة 2: بعد 200ms
            setTimeout(() => triggerEvent(data), 200);

            // محاولة 3: بعد 500ms
            setTimeout(() => triggerEvent(data), 500);

            // محاولة 4: بعد 1000ms
            setTimeout(() => triggerEvent(data), 1000);
        }
        function triggerEvent(data) {
            try {
                const event = new CustomEvent('show-firebase-notification', {
                    detail: data
                });
                window.dispatchEvent(event);
                console.log('✅ حدث الإشعار أُرسل');
            } catch (e) {
                console.log('⚠️ لم ينجح إرسال الحدث هذه المرة');
            }
        }

        // ========== بدء العملية ==========
        window.addEventListener('load', function () {
            console.log('📱 الصفحة تم تحميلها');

            // انتظر حتى يتم تحميل جميع الملفات
            setTimeout(function () {
                startFirebaseProcess();
            }, 2000);
        });

    </script>

</head>

<body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}">
    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>
    @include('components.notification.firebase-notification')


    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar Start ===== -->
        @include('layouts.sidebar')

        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Small Device Overlay Start -->
            <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
            <!-- Small Device Overlay End -->

            <!-- ===== Header Start ===== -->
            @include('layouts.header')
            <!-- ===== Header End ===== -->



            <!-- ===== Main Content Start ===== -->
            <main class="p-4 md:p-6">
                <!-- ===== Breadcrumb Start -->
                @include('layouts.Breadcrumb')
                <!-- ===== Breadcrumb End -->
                @yield('content')
            </main>

            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->
    @yield('script')
    <script defer src="{{ asset('tailadmin/build/bundle.js') }}"></script>
    @livewireScripts
    <script>
        function autoAssignSystem() {
            return {
                autoAssignEnabled: false,
                message: '',
                messageType: 'success',

                async init() {
                    // جلب الإعدادات الحالية من السيرفر
                    await this.loadCurrentSettings();
                },

                async loadCurrentSettings() {
                    try {
                        const response = await fetch('{{ route("system-settings.auto-assign.get") }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();
                        this.autoAssignEnabled = data.auto_assign_enabled;

                    } catch (error) {
                        console.error('Error loading settings:', error);
                        this.showMessage('خطأ في تحميل الإعدادات', 'error');
                    }
                },

                async updateAutoAssignSetting(enabled) {
                    try {
                        const response = await fetch('{{ route("system-settings.auto-assign.update") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                enabled: enabled
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.autoAssignEnabled = enabled;
                            this.showMessage(data.message, 'success');

                            // إعادة تحميل الصفحة بعد التحديث إذا لزم الأمر
                            setTimeout(() => {
                                // window.location.reload();
                            }, 1500);
                        } else {
                            this.showMessage(data.message, 'error');
                            // التراجع عن التغيير في حالة الخطأ
                            this.autoAssignEnabled = !enabled;
                        }

                    } catch (error) {
                        console.error('Error updating setting:', error);
                        this.showMessage('حدث خطأ أثناء حفظ الإعدادات', 'error');
                        this.autoAssignEnabled = !enabled;
                    }
                },

                showMessage(text, type = 'success') {
                    this.message = text;
                    this.messageType = type;

                    setTimeout(() => {
                        this.message = '';
                    }, 3000);
                }
            }
        }

    </script>
</body>

</html>