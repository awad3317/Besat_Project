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
    <link href="{{ asset('tailadmin/build/style.css') }}?v=1.0.1" rel="stylesheet">
    @livewireStyles
    @yield('style')
    
    <!-- مكتبات Pusher و Firebase -->
    <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js"></script>

    <!-- كود Firebase -->
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

        function isFirebaseLoaded() {
            if (typeof firebase === 'undefined' || typeof firebase.initializeApp === 'undefined' || typeof firebase.messaging === 'undefined') {
                return false;
            }
            return true;
        }

        function initializeFirebase() {
            if (!isFirebaseLoaded()) {
                loadFirebaseManually();
                return false;
            }

            try {
                let app = firebase.apps.length === 0 ? firebase.initializeApp(firebaseConfig) : firebase.apps[0];
                const messaging = firebase.messaging();
                return { app: app, messaging: messaging };
            } catch (error) {
                console.error('❌ خطأ في تهيئة Firebase:', error);
                return null;
            }
        }

        function loadFirebaseManually() {
            const firebaseAppScript = document.createElement('script');
            firebaseAppScript.src = 'https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js';
            firebaseAppScript.onload = function () {
                const firebaseMessagingScript = document.createElement('script');
                firebaseMessagingScript.src = 'https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js';
                firebaseMessagingScript.onload = function () {
                    setTimeout(() => startFirebaseProcess(), 1000);
                };
                document.head.appendChild(firebaseMessagingScript);
            };
            document.head.appendChild(firebaseAppScript);
        }

        async function startFirebaseProcess() {
            const firebaseInit = initializeFirebase();
            if (!firebaseInit) return;

            const { messaging } = firebaseInit;
            if (!('serviceWorker' in navigator)) return;

            try {
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                await navigator.serviceWorker.ready;

                if ("Notification" in window && Notification.permission === 'granted') {
                    await processToken(messaging, registration);
                } else if ("Notification" in window && Notification.permission === 'default') {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') await processToken(messaging, registration);
                }

                setupMessageListener(messaging);
            } catch (error) {
                console.error('❌ خطأ في عملية Firebase:', error);
            }
        }

        async function processToken(messaging, registration) {
            const storedToken = localStorage.getItem(TOKEN_STORAGE_KEY);
            if (storedToken && await validateToken(storedToken)) {
                return storedToken;
            }
            localStorage.removeItem(TOKEN_STORAGE_KEY);
            return await getNewToken(messaging, registration);
        }

        async function getNewToken(messaging, registration) {
            try {
                const token = await messaging.getToken({ vapidKey: vapidKey, serviceWorkerRegistration: registration });
                if (token) {
                    localStorage.setItem(TOKEN_STORAGE_KEY, token);
                    await sendTokenToServer(token);
                    return token;
                }
            } catch (error) {
                console.error('❌ خطأ في الحصول على التوكن:', error);
            }
            return null;
        }

        async function validateToken(token) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch("{{ route('firebase.validate-token') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ token: token })
                });
                if (response.ok) {
                    const data = await response.json();
                    return data.valid === true;
                }
            } catch (error) {}
            return false;
        }

        async function sendTokenToServer(token) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch("{{ route('firebase.token') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ fcm_token: token, _method: "PATCH" })
                });
            } catch (error) {}
        }

        let messageListenerSetup = false;
        function setupMessageListener(messaging) {
            if (messageListenerSetup) return;
            messaging.onMessage(function (payload) {
                setTimeout(() => {
                    let data = payload.notification ? {
                        title: payload.notification.title || 'إشعار جديد',
                        message: payload.notification.body || 'لديك إشعار',
                        showButtons: payload.data?.showButtons === 'true' || false
                    } : {
                        title: payload.data?.title || 'إشعار جديد',
                        message: payload.data?.body || 'لديك إشعار',
                        showButtons: payload.data?.showButtons === 'true' || false
                    };
                    sendEventWithRetry(data);
                }, 300);
            });
            messageListenerSetup = true;
        }

        function sendEventWithRetry(data) {
            setTimeout(() => triggerEvent(data), 0);
            setTimeout(() => triggerEvent(data), 200);
            setTimeout(() => triggerEvent(data), 500);
            setTimeout(() => triggerEvent(data), 1000);
        }

        function triggerEvent(data) {
            try {
                const event = new CustomEvent('show-firebase-notification', { detail: data });
                window.dispatchEvent(event);
            } catch (e) {}
        }

        window.addEventListener('load', function () {
            setTimeout(() => startFirebaseProcess(), 2000);
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
        @include('layouts.sidebar')

        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                class="fixed w-full h-screen z-9 bg-gray-900/50"></div>

            @include('layouts.header')

            <main class="p-4 md:p-6">
                @include('layouts.Breadcrumb')
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @yield('script')

    <script>
        function autoAssignSystem() {
            return {
                autoAssignEnabled: false,
                message: '',
                messageType: 'success',

                async init() {
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
                            body: JSON.stringify({ enabled: enabled })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.autoAssignEnabled = enabled;
                            this.showMessage(data.message, 'success');
                        } else {
                            this.showMessage(data.message, 'error');
                            this.autoAssignEnabled = !enabled;
                        }
                    } catch (error) {
                        this.showMessage('حدث خطأ أثناء حفظ الإعدادات', 'error');
                        this.autoAssignEnabled = !enabled;
                    }
                },

                showMessage(text, type = 'success') {
                    this.message = text;
                    this.messageType = type;
                    setTimeout(() => { this.message = ''; }, 3000);
                }
            }
        }
    </script>

    <!-- ========== كود استقبال الـ WebSockets (Reverb / Pusher) النظيف والمعتمد ========== -->
    <script>
        // 1. تفعيل السجلات في الكونسول للمتابعة
        Pusher.logToConsole = true;

        // 2. إنشاء الاتصال بسيرفر Reverb
        const pusher = new Pusher('bsk_live_9f8a7b6c5d4e3f2a', {
            cluster: 'mt1',
            wsHost: 'besat.tiyar.cc',
            wsPort: 443,
            wssPort: 443,
            forceTLS: true,
            enabledTransports: ['ws', 'wss']
        });

        // 3. الاشتراك في القناة
        const channel = pusher.subscribe('chat.support.1');

        console.log('📡 تم إعداد المستمع لقناة chat.support.1');

        // 4. تأكيد نجاح الاشتراك في القناة
        channel.bind('pusher:subscription_succeeded', function() {
            console.log('✅ تم الاشتراك بنجاح في القناة وجاهز لاستقبال الرسائل!');
        });

        // 5. الاستماع للحدث المباشر واستقبال الرسائل
        channel.bind('message.sent', function(data) {
            console.log('🎉 [وصلت الرسالة بنجاح!]:', data);
            alert('وصلت رسالة جديدة: ' + (data.body || ''));
        });

        // 6. خطة بديلة للربط بالاسم الكامل للكلاس
        channel.bind('App\\Events\\MessageSent', function(data) {
            console.log('🎉 [وصلت الرسالة عبر الكلاس الكامل!]:', data);
        });
    </script>
</body>
</html>