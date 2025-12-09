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
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
     <script>
        // تمرير البيانات من Laravel
        window.firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key', '') }}",
            authDomain: "{{ config('services.firebase.auth_domain', '') }}",
            projectId: "{{ config('services.firebase.project_id', '') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket', '') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id', '') }}",
            appId: "{{ config('services.firebase.app_id', '') }}",
            measurementId: "{{ config('services.firebase.measurement_id', '') }}"
        };
        
        window.vapidKey = "{{ env('FIREBASE_VAPID_KEY') }}";
        window.tokenRoute = "{{ route('firebase.token') }}";
        window.validateRoute = "{{ route('firebase.validate-token') }}";
    </script>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js";
        import { getMessaging, getToken, onMessage, isSupported } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js";

        const firebaseConfig = window.firebaseConfig;

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        const vapidKey = "{{ env('FIREBASE_VAPID_KEY') }}";

        // مفتاح التخزين المحلي
        const TOKEN_STORAGE_KEY = 'fcm_token_stored';

        // التحقق من التوكن المخزن
        async function checkAndUpdateToken() {
            try {
                // 1. التحقق من دعم Firebase
                const isFcmSupported = await isSupported();
                if (!isFcmSupported) return;

                // 2. التحقق من الإذن
                if (Notification.permission !== 'granted') {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') return;
                }

                // 3. التحقق إذا كان التوكن مخزناً مسبقاً
                const storedToken = localStorage.getItem(TOKEN_STORAGE_KEY);

                if (storedToken) {
                    console.log('✅ التوكن مخزن مسبقاً:', storedToken.substring(0, 20) + '...');

                    // التحقق مع السيرفر إذا كان التوكن صالحاً
                    const isValid = await validateTokenWithServer(storedToken);
                    if (isValid) {
                        console.log('✅ التوكن صالح، لا حاجة لتجديده');
                        return;
                    } else {
                        console.log('🔄 التوكن غير صالح، جاري التجديد...');
                        localStorage.removeItem(TOKEN_STORAGE_KEY);
                    }
                }

                // 4. الحصول على توكن جديد
                await getNewToken();

            } catch (error) {
                console.error('❌ خطأ:', error);
            }
        }

        // الحصول على توكن جديد
        async function getNewToken() {
            try {
                const token = await getToken(messaging, { vapidKey: vapidKey });

                if (token) {
                    console.log('✅ تم الحصول على توكن جديد:', token.substring(0, 20) + '...');

                    // تخزين التوكن محلياً
                    localStorage.setItem(TOKEN_STORAGE_KEY, token);

                    // إرسال التوكن للسيرفر
                    await sendTokenToServer(token);
                }
            } catch (error) {
                console.error('❌ خطأ في الحصول على التوكن:', error);
            }
        }

        // التحقق من صحة التوكن مع السيرفر
        async function validateTokenWithServer(token) {
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

                const data = await response.json();
                return data.valid === true;
            } catch (error) {
                console.error('❌ خطأ في التحقق:', error);
                return false;
            }
        }

        // إرسال التوكن للسيرفر
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
                console.error('❌ خطأ في الإرسال:', error);
            }
        }

        // استقبال الإشعارات
        onMessage(messaging, (payload) => {
            console.log('📨 إشعار مباشر:', payload);

            if (payload.notification) {
                const title = payload.notification.title || 'إشعار جديد';
                const body = payload.notification.body || 'لديك إشعار';

                // عرض إشعار بسيط
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        text: body,
                        icon: 'info',
                        timer: 3000
                    });
                } else {
                    alert(`${title}\n${body}`);
                }
            }
        });

        // البدء بعد تحميل الصفحة
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(checkAndUpdateToken, 1000);
        });
    </script>

</head>

<body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{ 'dark bg-gray-900': darkMode === true }">
    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => { setTimeout(() => loaded = false, 500) })"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>

    <!-- ===== Preloader End ===== -->

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