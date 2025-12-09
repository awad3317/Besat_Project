importScripts('https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js');

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

// تهيئة Firebase في Service Worker
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// معالجة الإشعارات في الخلفية
messaging.onBackgroundMessage((payload) => {
    
    const notificationTitle = payload.notification?.title || 'إشعار بسات';
    const notificationOptions = {
        body: payload.notification?.body || 'لديك إشعار جديد',
        icon: payload.notification?.icon || '/logo.png',
        badge: '/logo.png',
        data: payload.data || {}
    };

    // عرض الإشعا
    self.registration.showNotification(notificationTitle, notificationOptions);
});

// معالجة نقر المستخدم على الإشعار
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] تم النقر على الإشعار:', event.notification);
    
    event.notification.close();
    
    // فتح صفحة التطبيق عند النقر على الإشعار
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                for (const client of clientList) {
                    if (client.url === '/' && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow('/');
                }
            })
    );
});