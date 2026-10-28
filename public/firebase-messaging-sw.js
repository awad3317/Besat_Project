// public/firebase-messaging-sw.js

// إصدار Firebase المتوافق مع Service Worker
try {
    importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');
    
    console.log('✅ Firebase SDK تم تحميله في Service Worker');
    
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
    
    // تهيئة Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    
    console.log('✅ Firebase مهيأ في Service Worker');
    
    // معالجة الإشعارات في الخلفية
    messaging.onBackgroundMessage(function(payload) {
        console.log('📨 إشعار خلفية:', payload);
        
        const notificationTitle = payload.notification?.title || payload.data?.title || 'إشعار بسات';
        const notificationOptions = {
            body: payload.notification?.body || payload.data?.body || 'لديك إشعار جديد',
            icon: payload.notification?.icon || '/tailadmin/build/src/images/user/Busat.png',
            badge: '/tailadmin/build/src/images/user/Busat.png',
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
    console.error('❌ خطأ في Service Worker:', error);
}

// معالجة نقر الإشعار
self.addEventListener('notificationclick', function(event) {
    console.log('👆 تم النقر على الإشعار:', event.notification);
    
    event.notification.close();
    
    const urlToOpen = event.notification.data?.url || '/request';
    
    event.waitUntil(
        clients.matchAll({ 
            type: 'window', 
            includeUncontrolled: true 
        }).then(function(clientList) {
            for (const client of clientList) {
                if ('focus' in client) {
                    if (urlToOpen && 'navigate' in client) {
                        client.navigate(urlToOpen);
                    }
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
