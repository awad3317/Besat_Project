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
    <script src="https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js"></script>
    
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- كود Firebase يعمل على السيرفر -->
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
        
        // ========== متغيرات عامة ==========
        let firebaseApp = null;
        let firebaseMessaging = null;
        
        // ========== دالة التحقق من Firebase SDK ==========
        function checkFirebaseSDK() {
            console.log('🔥 التحقق من Firebase SDK:', {
                firebase: typeof firebase,
                firebaseApp: typeof firebase !== 'undefined' ? typeof firebase.app : 'غير محمل',
                firebaseMessaging: typeof firebase !== 'undefined' ? typeof firebase.messaging : 'غير محمل'
            });
            
            return typeof firebase !== 'undefined' && 
                   typeof firebase.initializeApp !== 'undefined' &&
                   typeof firebase.messaging !== 'undefined';
        }
        
        // ========== تهيئة Firebase ==========
        function initializeFirebase() {
            if (!checkFirebaseSDK()) {
                console.error('❌ Firebase SDK غير محمل بشكل صحيح');
                return false;
            }
            
            try {
                // التحقق إذا كان Firebase مهيأ مسبقاً
                if (firebase.apps.length === 0) {
                    firebaseApp = firebase.initializeApp(firebaseConfig);
                    console.log('✅ Firebase تم تهيئته للمرة الأولى');
                } else {
                    firebaseApp = firebase.app();
                    console.log('✅ Firebase موجود مسبقاً');
                }
                
                firebaseMessaging = firebase.messaging();
                console.log('✅ Firebase Messaging جاهز');
                return true;
                
            } catch (error) {
                console.error('❌ خطأ في تهيئة Firebase:', error);
                return false;
            }
        }
        
        // ========== تسجيل Service Worker ==========
        async function registerServiceWorker() {
            if (!('serviceWorker' in navigator)) {
                console.error('❌ Service Worker غير مدعوم في هذا المتصفح');
                return null;
            }
            
            try {
                console.log('🔄 جاري تسجيل Service Worker...');
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                console.log('✅ Service Worker مسجل بنجاح:', registration.scope);
                
                // الانتظار حتى يصبح Service Worker نشطاً
                await navigator.serviceWorker.ready;
                console.log('✅ Service Worker نشط وجاهز');
                
                return registration;
                
            } catch (error) {
                console.error('❌ فشل تسجيل Service Worker:', error);
                return null;
            }
        }
        
        // ========== التحقق من التوكن المخزن ==========
        async function checkStoredToken() {
            const storedToken = localStorage.getItem(TOKEN_STORAGE_KEY);
            
            if (!storedToken) {
                console.log('ℹ️ لا يوجد توكن مخزن');
                return null;
            }
            
            console.log('✅ التوكن مخزن مسبقاً:', storedToken.substring(0, 20) + '...');
            
            // التحقق من صحة التوكن مع السيرفر
            const isValid = await validateToken(storedToken);
            if (isValid) {
                console.log('✅ التوكن صالح');
                return storedToken;
            } else {
                console.log('🔄 التوكن غير صالح، سيتم حذفه');
                localStorage.removeItem(TOKEN_STORAGE_KEY);
                return null;
            }
        }
        
        // ========== الحصول على توكن جديد ==========
        async function getNewToken(serviceWorkerRegistration) {
            try {
                console.log('🔄 جاري طلب توكن جديد من Firebase...');
                
                const token = await firebaseMessaging.getToken({
                    vapidKey: vapidKey,
                    serviceWorkerRegistration: serviceWorkerRegistration
                });
                
                if (!token) {
                    console.log('⚠️ Firebase لم يعطينا توكن');
                    return null;
                }
                
                console.log('✅ تم الحصول على توكن جديد:', token.substring(0, 20) + '...');
                
                // حفظ التوكن محلياً
                localStorage.setItem(TOKEN_STORAGE_KEY, token);
                
                // إرسال التوكن للسيرفر
                await sendTokenToServer(token);
                
                return token;
                
            } catch (error) {
                console.error('❌ خطأ في الحصول على التوكن:', error);
                console.error('رمز الخطأ:', error.code);
                console.error('رسالة الخطأ:', error.message);
                return null;
            }
        }
        
        // ========== التحقق من صحة التوكن مع السيرفر ==========
        async function validateToken(token) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    console.error('❌ CSRF Token غير موجود');
                    return false;
                }
                
                const response = await fetch("{{ route('firebase.validate-token') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ token: token })
                });
                
                if (!response.ok) {
                    console.error('❌ استجابة غير صالحة من السيرفر:', response.status);
                    return false;
                }
                
                const data = await response.json();
                return data.valid === true;
                
            } catch (error) {
                console.error('❌ خطأ في التحقق من التوكن:', error);
                return false;
            }
        }
        
        // ========== إرسال التوكن للسيرفر ==========
        async function sendTokenToServer(token) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                if (!csrfToken) {
                    console.error('❌ CSRF Token غير موجود');
                    return false;
                }
                
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
                    console.log('✅ تم إرسال التوكن للسيرفر بنجاح');
                    return true;
                } else {
                    console.error('❌ فشل إرسال التوكن:', response.status);
                    return false;
                }
                
            } catch (error) {
                console.error('❌ خطأ في إرسال التوكن:', error);
                return false;
            }
        }
        
        // ========== التحقق من إذن الإشعارات ==========
        async function checkNotificationPermission() {
            if (!("Notification" in window)) {
                console.error('❌ هذا المتصفح لا يدعم الإشعارات');
                return false;
            }
            
            console.log('🔔 حالة إذن الإشعارات الحالية:', Notification.permission);
            
            if (Notification.permission === 'granted') {
                console.log('✅ الإذن ممنوح بالفعل');
                return true;
            }
            
            if (Notification.permission === 'denied') {
                console.error('❌ الإذن مرفوض من قبل المستخدم');
                return false;
            }
            
            // إذن الإشعارات هو 'default'
            console.log('🔔 طلب إذن الإشعارات من المستخدم...');
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('✅ تم منح الإذن');
                return true;
            } else {
                console.error('❌ المستخدم رفض الإذن');
                return false;
            }
        }
        
        // ========== إعداد استقبال الإشعارات في الواجهة الأمامية ==========
        function setupMessageListener() {
            firebaseMessaging.onMessage(function(payload) {
                console.log('📨 إشعار مباشر في الواجهة الأمامية:', payload);
                
                if (payload.notification) {
                    showNotification(
                        payload.notification.title || 'إشعار جديد',
                        payload.notification.body || 'لديك إشعار'
                    );
                }
            });
            
            console.log('✅ تم إعداد مستمع الإشعارات في الواجهة الأمامية');
        }
        
        // ========== عرض الإشعار ==========
        function showNotification(title, body) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: body,
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    background: '#10B981',
                    color: 'white'
                });
            } else {
                alert(title + '\n' + body);
            }
        }
        
        // ========== الدالة الرئيسية لتشغيل كل شيء ==========
        async function initializeFirebaseMessaging() {
            console.log('🚀 بدء إعداد Firebase Messaging...');
            
            // 1. تهيئة Firebase
            if (!initializeFirebase()) {
                console.error('❌ فشل تهيئة Firebase');
                return;
            }
            
            // 2. تسجيل Service Worker
            const serviceWorkerRegistration = await registerServiceWorker();
            if (!serviceWorkerRegistration) {
                console.error('❌ فشل تسجيل Service Worker');
                return;
            }
            
            // 3. التحقق من إذن الإشعارات
            if (!await checkNotificationPermission()) {
                console.error('❌ لا يوجد إذن للإشعارات');
                return;
            }
            
            // 4. التحقق من التوكن المخزن
            let token = await checkStoredToken();
            
            // 5. إذا لم يكن هناك توكن صالح، احصل على واحد جديد
            if (!token) {
                token = await getNewToken(serviceWorkerRegistration);
            }
            
            // 6. إذا حصلنا على توكن، أعد استقبال الإشعارات
            if (token) {
                setupMessageListener();
                console.log('🎉 Firebase Messaging جاهز للعمل!');
            } else {
                console.error('❌ لم نتمكن من الحصول على توكن صالح');
            }
        }
        
        // ========== بدء العملية عند تحميل الصفحة ==========
        window.addEventListener('load', function() {
            console.log('📱 الصفحة تم تحميلها، جاري بدء إعداد Firebase...');
            
            // الانتظار قليلاً قبل البدء
            setTimeout(function() {
                initializeFirebaseMessaging();
            }, 1000);
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