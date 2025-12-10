<!-- في الملف firebase-notification.blade.php -->
<div x-data="{
    show: false,
    title: '',
    message: '',
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
        this.showButtons = data.showButtons || false;
    },
    closeNotification() {
        this.show = false;
    },
    handleAction() {
        // يمكنك إضافة منطق إضافي هنا عند النقر على 'متابعة'
        console.log('تم النقر على متابعة');
        this.closeNotification();
    }
}">
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-[-20px]"
         class="fixed top-16 left-1/2 transform -translate-x-1/2 z-99999 w-full max-w-md rtl">
        
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start gap-3">
                <!-- الأيقونة على اليسار -->
                <div class="flex-shrink-0 bg-orange-50 text-orange-500">
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
                        <h5 class="text-base font-medium text-gray-800 dark:text-white/90 mb-1"
                            x-text="title">
                        </h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400"
                           x-text="message">
                        </p>
                    </div>
                
                    <!-- حالة بدون أزرار -->
                    <template x-if="!showButtons">
                        <div class="flex items-center justify-between">
                            <button
                                type="button"
                                @click="closeNotification()"
                                class="text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                فتح
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>