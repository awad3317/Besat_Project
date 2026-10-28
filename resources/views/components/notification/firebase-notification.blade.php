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
         class="fixed top-16 left-1/2 transform -translate-x-1/2 z-99999 w-full max-w-md rtl">
        
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start gap-3">
                <!-- الأيقونة -->
                <div class="flex-shrink-0 rounded-lg bg-orange-50 p-2 text-orange-500 dark:bg-orange-500/15">
                    <svg width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.814 4.75L4.78516 16.0352H11.1859L11.1859 23.25L19.2148 11.9648L12.814 11.9648V4.75Z"
                            stroke="#dc6803"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <!-- المحتوى النصي والأزرار -->
                <div class="flex-1">
                    <!-- العنوان والرسالة -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-base font-medium text-gray-800 dark:text-white/90 mb-1"
                                x-text="title">
                            </h5>
                            <button type="button" @click="closeNotification()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                ✕
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400"
                           x-text="message">
                        </p>
                    </div>
                
                    <!-- الأزرار -->
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="openNotification()"
                            class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
                            فتح الطلب
                        </button>
                        <button
                            type="button"
                            @click="closeNotification()"
                            class="text-xs font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
