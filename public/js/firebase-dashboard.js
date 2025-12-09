// public/js/firebase-dashboard.js

class FirebaseDashboard {
    constructor(config) {
        this.config = config;
        this.app = null;
        this.messaging = null;
        this.initialized = false;
        this.TOKEN_STORAGE_KEY = 'fcm_token_stored';
    }
    
    // تهيئة Firebase
    init() {
        try {
            // التحقق من تحميل Firebase SDK
            if (typeof firebase === 'undefined') {
                throw new Error('Firebase SDK غير محمل');
            }
            
            // التحقق من الإعدادات
            if (!this.config.apiKey || !this.config.projectId) {
                throw new Error('إعدادات Firebase غير مكتملة');
            }
            
            // تهيئة Firebase
            if (firebase.apps.length === 0) {
                this.app = firebase.initializeApp(this.config);
            } else {
                this.app = firebase.app();
            }
            
            this.messaging = firebase.messaging();
            this.initialized = true;
            
            console.log('✅ Firebase Dashboard مهيأ');
            return true;
            
        } catch (error) {
            console.error('❌ خطأ في تهيئة Firebase Dashboard:', error);
            return false;
        }
    }
    
    // تسجيل Service Worker
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            console.error('❌ Service Worker غير مدعوم');
            return null;
        }
        
        try {
            console.log('🔄 تسجيل Service Worker...');
            
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('✅ Service Worker مسجل:', registration.scope);
            
            await navigator.serviceWorker.ready;
            
            // إرسال الإعدادات إلى Service Worker
            if (registration.active) {
                registration.active.postMessage({
                    type: 'FIREBASE_CONFIG',
                    config: this.config
                });
                console.log('📡 تم إرسال الإعدادات إلى Service Worker');
            }
            
            return registration;
            
        } catch (error) {
            console.error('❌ فشل تسجيل Service Worker:', error);
            return null;
        }
    }
    
    // إعداد Firebase بالكامل
    async setup() {
        try {
            // 1. تهيئة Firebase
            if (!this.init()) {
                return;
            }
            
            // 2. تسجيل Service Worker
            const registration = await this.registerServiceWorker();
            if (!registration) {
                return;
            }
            
            // 3. التحقق من الإذن
            if (!await this.checkPermission()) {
                return;
            }
            
            // 4. التحقق من التوكن المخزن
            const storedToken = localStorage.getItem(this.TOKEN_STORAGE_KEY);
            
            if (storedToken) {
                console.log('✅ التوكن مخزن مسبقاً');
                
                const isValid = await this.validateToken(storedToken);
                if (isValid) {
                    console.log('✅ التوكن صالح');
                    this.setupMessageListener();
                    return;
                } else {
                    console.log('🔄 التوكن غير صالح');
                    localStorage.removeItem(this.TOKEN_STORAGE_KEY);
                }
            }
            
            // 5. الحصول على توكن جديد
            await this.getNewToken(registration);
            
        } catch (error) {
            console.error('❌ خطأ في إعداد Firebase:', error);
        }
    }
    
    // التحقق من إذن الإشعارات
    async checkPermission() {
        if (!("Notification" in window)) {
            console.log('❌ المتصفح لا يدعم الإشعارات');
            return false;
        }
        
        if (Notification.permission === 'granted') {
            console.log('✅ الإذن موجود');
            return true;
        }
        
        if (Notification.permission === 'denied') {
            console.log('❌ الإذن مرفوض');
            return false;
        }
        
        console.log('🔔 طلب إذن الإشعارات...');
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('✅ تم منح الإذن');
            return true;
        } else {
            console.log('❌ المستخدم رفض الإذن');
            return false;
        }
    }
    
    // الحصول على توكن جديد
    async getNewToken(registration) {
        try {
            const token = await this.messaging.getToken({
                vapidKey: this.config.vapidKey,
                serviceWorkerRegistration: registration
            });
            
            if (token) {
                console.log('✅ تم الحصول على توكن جديد');
                
                localStorage.setItem(this.TOKEN_STORAGE_KEY, token);
                await this.sendTokenToServer(token);
                
                this.setupMessageListener();
            }
        } catch (error) {
            console.error('❌ خطأ في الحصول على التوكن:', error);
        }
    }
    
    // التحقق من صحة التوكن
    async validateToken(token) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(this.config.validateRoute, {
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
            console.error('❌ خطأ في التحقق:', error);
        }
        return false;
    }
    
    // إرسال التوكن للسيرفر
    async sendTokenToServer(token) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(this.config.tokenRoute, {
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
    
    // إعداد استقبال الإشعارات
    setupMessageListener() {
        this.messaging.onMessage((payload) => {
            console.log('📨 إشعار مباشر:', payload);
            
            if (payload.notification) {
                this.showNotification(
                    payload.notification.title || 'إشعار جديد',
                    payload.notification.body || 'لديك إشعار'
                );
            }
        });
    }
    
    // عرض الإشعار
    showNotification(title, body) {
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
        }
    }
}

// جعل الكلاس متاحاً
window.FirebaseDashboard = FirebaseDashboard;