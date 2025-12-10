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

                if (payload.notification) {
                    // إرسال حدث لعرض الإشعار
                    const event = new CustomEvent('show-firebase-notification', {
                        detail: {
                            title: payload.notification.title || 'إشعار جديد',
                            message: payload.notification.body || 'لديك إشعار',
                            showButtons: payload.data?.showButtons === 'true' || false
                        }
                    });
                    window.dispatchEvent(event);
                } else if (payload.data) {
                    const event = new CustomEvent('show-firebase-notification', {
                        detail: {
                            title: payload.data.title || 'إشعار جديد',
                            message: payload.data.body || 'لديك إشعار',
                            showButtons: payload.data.showButtons === 'true' || false
                        }
                    });
                    window.dispatchEvent(event);
                }
            });

            messageListenerSetup = true;
            console.log('✅ تم إعداد مستمع الإشعارات');
        }
        // ========== عرض الإشعار ==========
        function showNotification(title, body) {
            alert(title + '\n' + body);
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

<body x-data="{ 
        page: 'ecommerce', 
        loaded: true, 
        darkMode: false, 
        stickyMenu: false, 
        sidebarToggle: false, 
        scrollTop: false,
        firebaseNotification: {
            show: false,
            title: '',
            message: '',
            showButtons: false
        },
        init() {
            this.darkMode = JSON.parse(localStorage.getItem('darkMode')) || false;
            this.$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
            
            // الاستماع لحدث عرض إشعار Firebase
            window.addEventListener('show-firebase-notification', (event) => {
                this.showFirebaseNotification(event.detail);
            });
        },
        showFirebaseNotification(data) {
            this.firebaseNotification.show = true;
            this.firebaseNotification.title = data.title;
            this.firebaseNotification.message = data.message;
            this.firebaseNotification.showButtons = data.showButtons;
            
            // إغلاق تلقائي بعد 5 ثواني إذا لم يكن هناك أزرار
            if (!data.showButtons) {
                setTimeout(() => {
                    if (this.firebaseNotification.show) {
                        this.firebaseNotification.show = false;
                    }
                }, 5000);
            }
        },
        closeFirebaseNotification() {
            this.firebaseNotification.show = false;
        }
    }" :class="{ 'dark bg-gray-900': darkMode === true }">
    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => { setTimeout(() => loaded = false, 500) })"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>

    <!-- ===== إشعار Firebase ===== -->
    <div x-show="firebaseNotification.show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-[-20px]"
        class="fixed top-4 right-4 z-99999 w-full max-w-md">

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start gap-3">
                <div>
                    <svg width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.814 4.75L4.78516 16.0352H11.1859L11.1859 23.25L19.2148 11.9648L12.814 11.9648V4.75Z"
                            stroke="#465FFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <div class="flex flex-1 flex-col items-start gap-3 sm:flex-row sm:items-center">
                    <div class="flex-1">
                        <h5 class="mb-1 text-base font-medium text-gray-800 dark:text-white/90"
                            x-text="firebaseNotification.title">
                        </h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="firebaseNotification.message">
                        </p>
                    </div>

                    <template x-if="firebaseNotification.showButtons">
                        <div class="flex w-full items-center gap-3 sm:w-auto">
                            <button type="button" @click="closeFirebaseNotification()"
                                class="flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                لاحقاً
                            </button>
                            <button type="button" @click="closeFirebaseNotification()"
                                class="flex justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                                متابعة
                            </button>
                        </div>
                    </template>

                    <template x-if="!firebaseNotification.showButtons">
                        <div class="flex w-full items-center justify-between">
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                الآن
                            </span>
                            <button type="button" @click="closeFirebaseNotification()"
                                class="text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                إغلاق
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
    <!-- ===== إشعار Firebase End ===== -->

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