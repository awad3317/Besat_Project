<!-- في الملف firebase-notification.blade.php -->
<div x-data="{
    show: false,
    title: '',
    message: '',
    url: '',
    showButtons: false,
    init() {
        window.firebaseNotificationReady = true;
        window.addEventListener('show-firebase-notification', (event) => {
            this.showNotification(event.detail);
        });
    },
    showNotification(data) {
        this.show = true;
        this.title = data.title || 'إشعار جديد';
        this.message = data.message || 'لديك إشعار';
        this.url = data.url || '';
        this.showButtons = data.showButtons || false;
    },
    closeNotification() {
        this.show = false;
    },
    openNotification() {
        if (this.url) {
            window.location.href = this.url;
        } else {
            this.closeNotification();
        }
    }
}">
    <div x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-[-20px]"
         class="fixed top-16 left-1/2 transform -translate-x-1/2 z-99999 w-full max-w-md rtl"
         style="padding: 0 16px;">
        
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800"
             style="border-right: 4px solid #dc6803;">
            <div class="flex items-start gap-3">
                <!-- أيقونة الإشعار البارزة -->
                <div style="width: 46px; height: 46px; min-width: 46px; border-radius: 12px; background: rgba(220, 104, 3, 0.14); display: flex; align-items: center; justify-content: center; position: relative;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 17H4a2 2 0 0 1-2-2v-4l2.48-5.78A2 2 0 0 1 6.32 4h11.36a2 2 0 0 1 1.84 1.22L22 11v4a2 2 0 0 1-2 2h-1" stroke="#dc6803" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="7.5" cy="17.5" r="2.5" stroke="#dc6803" stroke-width="1.8"/>
                        <circle cx="16.5" cy="17.5" r="2.5" stroke="#dc6803" stroke-width="1.8"/>
                        <path d="M10 17h4" stroke="#dc6803" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span style="position: absolute; top: -3px; right: -3px; width: 10px; height: 10px; border-radius: 9999px; background: #dc6803; border: 2px solid #fff;"></span>
                </div>

                <!-- المحتوى النصي والأزرار -->
                <div class="flex-1">
                    <!-- العنوان والرسالة -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-1"
                                x-text="title">
                            </h5>
                            <button type="button" @click="closeNotification()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                ✕
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400" style="line-height: 1.5;"
                           x-text="message">
                        </p>
                    </div>
                
                    <!-- الأزرار مع الأيقونات -->
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="openNotification()"
                            class="flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors"
                            style="background-color: #dc6803; color: #fff; padding: 6px 14px; border-radius: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>فتح الطلب</span>
                        </button>
                        <button
                            type="button"
                            @click="closeNotification()"
                            class="text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                            style="padding: 6px 10px;">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
