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
            firebaseAppScript.onload = function() {
                console.log('✅ firebase-app-compat.js تم تحميله');
                
                const firebaseMessagingScript = document.createElement('script');
                firebaseMessagingScript.src = 'https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js';
                firebaseMessagingScript.onload = function() {
                    console.log('✅ firebase-messaging-compat.js تم تحميله');
                    
                    // أعط فرصة للصفحة لتهيئة Firebase
                    setTimeout(function() {
                        startFirebaseProcess();
                    }, 1000);
                };
                firebaseMessagingScript.onerror = function() {
                    console.error('❌ فشل تحميل firebase-messaging-compat.js');
                };
                document.head.appendChild(firebaseMessagingScript);
            };
            firebaseAppScript.onerror = function() {
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
        function setupMessageListener(messaging) {
            messaging.onMessage(function(payload) {
                console.log('📨 إشعار مباشر:', payload);
                
                if (payload.notification) {
                    // عرض الإشعار باستخدام نظام Alpine.js
                    showAlpineNotification(
                        payload.notification.title || 'إشعار جديد',
                        payload.notification.body || 'لديك إشعار'
                    );
                } else if (payload.data) {
                    // إذا كان الإشعار يحتوي على بيانات إضافية
                    showAlpineNotification(
                        payload.data.title || payload.notification?.title || 'إشعار جديد',
                        payload.data.body || payload.notification?.body || 'لديك إشعار',
                        payload.data.type || 'info',
                        {
                            duration: 5000,
                            showLaterButton: payload.data.showLaterButton || false,
                            showActionButton: payload.data.showActionButton || false,
                            actionText: payload.data.actionText || 'متابعة',
                            actionCallback: payload.data.actionCallback || null
                        }
                    );
                }
            });
            
            console.log('✅ تم إعداد مستمع الإشعارات');
        }
        
        // ========== دالة لعرض إشعار باستخدام Alpine.js ==========
        function showAlpineNotification(title, message, type = 'info', options = {}) {
            const event = new CustomEvent('alpine-notification', {
                detail: { 
                    title, 
                    message, 
                    type, 
                    options: {
                        duration: options.duration || 5000,
                        showLaterButton: options.showLaterButton || false,
                        showActionButton: options.showActionButton || false,
                        actionText: options.actionText || 'متابعة',
                        actionCallback: options.actionCallback || null
                    }
                }
            });
            window.dispatchEvent(event);
        }
        
        // ========== بدء العملية ==========
        window.addEventListener('load', function() {
            console.log('📱 الصفحة تم تحميلها');
            
            // انتظر حتى يتم تحميل جميع الملفات
            setTimeout(function() {
                startFirebaseProcess();
            }, 2000);
        });
    </script>

</head>

<body
    x-data="{ 
        page: 'ecommerce', 
        loaded: true, 
        darkMode: false, 
        stickyMenu: false, 
        sidebarToggle: false, 
        scrollTop: false,
        notifications: [],
        init() {
            this.darkMode = JSON.parse(localStorage.getItem('darkMode')) || false;
            
            // مراقبة التغيير في الوضع الداكن
            this.$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
            
            // تحميل الإشعارات المخزنة محلياً
            this.loadNotifications();
            
            // الاستماع لإشعارات Firebase
            window.addEventListener('alpine-notification', (event) => {
                this.addNotification(event.detail);
            });
            
            // تنظيف الإشعارات القديمة كل ساعة
            setInterval(() => this.cleanupOldNotifications(), 60 * 60 * 1000);
        },
        
        // تحميل الإشعارات
        loadNotifications() {
            const saved = localStorage.getItem('notifications');
            if (saved) {
                try {
                    this.notifications = JSON.parse(saved);
                } catch (e) {
                    this.notifications = [];
                }
            }
        },
        
        // حفظ الإشعارات
        saveNotifications() {
            localStorage.setItem('notifications', JSON.stringify(this.notifications));
        },
        
        // إضافة إشعار جديد
        addNotification(detail) {
            const id = Date.now() + Math.random();
            const notification = {
                id,
                title: detail.title,
                message: detail.message,
                type: detail.type,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                showLaterButton: detail.options.showLaterButton || false,
                showActionButton: detail.options.showActionButton || false,
                actionText: detail.options.actionText || 'متابعة',
                actionCallback: detail.options.actionCallback || null,
                createdAt: new Date().toISOString()
            };
            
            this.notifications.unshift(notification);
            this.saveNotifications();
            
            // إزالة تلقائية بعد الوقت المحدد
            const duration = detail.options.duration || 5000;
            if (duration > 0) {
                setTimeout(() => {
                    this.removeNotification(id);
                }, duration);
            }
        },
        
        // إزالة إشعار
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
            this.saveNotifications();
        },
        
        // مسح جميع الإشعارات
        clearAllNotifications() {
            this.notifications = [];
            this.saveNotifications();
        },
        
        // تنفيذ إجراء
        executeAction(callback) {
            if (callback) {
                try {
                    eval(callback);
                } catch (error) {
                    console.error('خطأ في تنفيذ الإجراء:', error);
                }
            }
        },
        
        // تنظيف الإشعارات القديمة
        cleanupOldNotifications() {
            const now = new Date();
            const oldNotifications = this.notifications.filter(n => {
                const created = new Date(n.createdAt);
                const diff = now - created;
                return diff > (24 * 60 * 60 * 1000); // إزالة الإشعارات الأقدم من 24 ساعة
            });
            
            if (oldNotifications.length > 0) {
                this.notifications = this.notifications.filter(n => {
                    const created = new Date(n.createdAt);
                    const diff = now - created;
                    return diff <= (24 * 60 * 60 * 1000);
                });
                this.saveNotifications();
            }
        }
    }"
    :class="{ 'dark bg-gray-900': darkMode === true }">
    
    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => { setTimeout(() => loaded = false, 500) })"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>
    <!-- ===== Preloader End ===== -->

    <!-- ===== نظام الإشعارات ===== -->
    <div class="fixed inset-0 pointer-events-none z-50"
         @alpine-notification.window="addNotification($event.detail)">
        
        <div class="absolute top-4 right-4 flex flex-col gap-3 max-h-[calc(100vh-2rem)] overflow-y-auto pointer-events-auto"
             x-show="notifications.length > 0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <template x-for="notification in notifications" :key="notification.id">
                <div class="w-full max-w-md rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-4 shadow-lg">
                    <div class="flex items-start gap-3">
                        <!-- الأيقونة -->
                        <div class="flex-shrink-0 text-gray-600 dark:text-gray-400">
                            <svg width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.814 4.75L4.78516 16.0352H11.1859L11.1859 23.25L19.2148 11.9648L12.814 11.9648V4.75Z" 
                                      stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        
                        <!-- المحتوى -->
                        <div class="flex-1 min-w-0">
                            <div class="mb-3">
                                <h5 class="text-base font-medium text-gray-800 dark:text-white/90 mb-1" 
                                    x-text="notification.title"></h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400" 
                                   x-text="notification.message"></p>
                            </div>
                            
                            <!-- الأزرار -->
                            <div class="flex flex-wrap items-center gap-3">
                                <template x-if="notification.showLaterButton">
                                    <button type="button"
                                            @click="removeNotification(notification.id)"
                                            class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                                        لاحقاً
                                    </button>
                                </template>
                                
                                <template x-if="notification.showActionButton">
                                    <button type="button"
                                            @click="executeAction(notification.actionCallback); removeNotification(notification.id)"
                                            class="inline-flex justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                        <span x-text="notification.actionText"></span>
                                    </button>
                                </template>
                                
                                <template x-if="!notification.showLaterButton && !notification.showActionButton">
                                    <div class="flex items-center justify-between w-full mt-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-500" 
                                              x-text="notification.time"></span>
                                        <button type="button" 
                                                @click="removeNotification(notification.id)"
                                                class="text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                                            إغلاق
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- زر الإغلاق -->
                        <button type="button" 
                                @click="removeNotification(notification.id)"
                                class="flex-shrink-0 -mt-1 -mr-1 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
            
            <!-- زر مسح الكل -->
            <div x-show="notifications.length > 1" class="text-center mt-2">
                <button @click="clearAllNotifications()"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                    مسح جميع الإشعارات
                </button>
            </div>
        </div>
    </div>
    <!-- ===== نظام الإشعارات End ===== -->

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
    
    <!-- كود Alpine.js للإشعارات الإضافية -->
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
        
        // دالة مساعدة لعرض إشعار تجريبي (للاختبار)
        function testNotification() {
            const event = new CustomEvent('alpine-notification', {
                detail: { 
                    title: 'تحديث جديد! متاح',
                    message: 'استمتع بوظائف محسنة وتحسينات جديدة.',
                    type: 'update',
                    options: {
                        duration: 8000,
                        showLaterButton: true,
                        showActionButton: true,
                        actionText: 'تحديث الآن',
                        actionCallback: "alert('جاري التحديث...')"
                    }
                }
            });
            window.dispatchEvent(event);
        }
    </script>
</body>

</html>