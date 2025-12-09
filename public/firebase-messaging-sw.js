// public/firebase-messaging-sw.js

// هذا Service Worker سيعمل على السيرفر
(function() {
    'use strict';
    
    // إعدادات Firebase
    const firebaseConfig = {
        apiKey: "AIzaSyCbIq9vJ6JLD0Rtk0S_CUa09W5uI46DXfs",
        authDomain: "besat-91f88.firebaseapp.com",
        projectId: "besat-91f88",
        storageBucket: "besat-91f88.firebasestorage.app",
        messagingSenderId: "463642053508",
        appId: "1:463642053508:web:b42c1a10046193c6466bd1",
        measurementId: "G-PEE4VVH75H"
    };
    
    // محاولة تحميل Firebase
    try {
        importScripts('https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js');
        importScripts('https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js');
        
        console.log('✅ Firebase SDK تم تحميله في Service Worker');
        
        // تهيئة Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        
        console.log('✅ Firebase مهيأ في Service Worker');
        
        // معالجة الإشعارات في الخلفية
        messaging.onBackgroundMessage(function(payload) {
            console.log('📨 إشعار خلفية:', payload);
            
            const notificationTitle = payload.notification?.title || 'إشعار بسات';
            const notificationOptions = {
                body: payload.notification?.body || 'لديك إشعار جديد',
                icon: payload.notification?.icon || '/logo.png',
                badge: '/logo.png',
                data: payload.data || {},
                actions: [
                    {
                        action: 'open',
                        title: 'فتح'
                    }
                ]
            };

            self.registration.showNotification(notificationTitle, notificationOptions);
        });
        
    } catch (error) {
        console.error('❌ خطأ في تحميل Firebase SDK:', error);
        
        // Fallback: استخدام Push API مباشرة
        self.addEventListener('push', function(event) {
            console.log('📨 Push event received (Fallback)');
            
            let payload;
            try {
                payload = event.data ? event.data.json() : {};
            } catch (e) {
                payload = {
                    notification: {
                        title: 'إشعار بسات',
                        body: 'لديك إشعار جديد'
                    }
                };
            }
            
            const notificationTitle = payload.notification?.title || 'إشعار بسات';
            const notificationOptions = {
                body: payload.notification?.body || 'لديك إشعار جديد',
                icon: '/logo.png',
                badge: '/logo.png',
                data: payload.data || {}
            };

            event.waitUntil(
                self.registration.showNotification(notificationTitle, notificationOptions)
            );
        });
    }
    
    // معالجة نقر الإشعار
    self.addEventListener('notificationclick', function(event) {
        console.log('👆 تم النقر على الإشعار:', event.notification);
        
        event.notification.close();
        
        const urlToOpen = event.notification.data?.url || '/dashboard';
        
        event.waitUntil(
            clients.matchAll({ 
                type: 'window', 
                includeUncontrolled: true 
            }).then(function(clientList) {
                for (const client of clientList) {
                    if (client.url.includes('/dashboard') && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
        );
    });
    
    // تهيئة Service Worker
    self.addEventListener('install', function(event) {
        console.log('⚙️ Service Worker installing...');
        self.skipWaiting();
    });
    
    self.addEventListener('activate', function(event) {
        console.log('✅ Service Worker activated');
        event.waitUntil(clients.claim());
    });
    
})();