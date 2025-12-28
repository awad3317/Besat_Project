<div x-show="showErrorModal" class="fixed inset-0 z-50 flex items-center justify-center p-5" style="display: none;">
    <div x-show="showErrorModal" class="fixed inset-0 flex items-center justify-center p-5 z-99999">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900 shadow-xl">
            <div class="text-center py-4">
                <div class="relative flex items-center justify-center z-1 mb-7">
                    <svg style="color:#fee2e2" width="90" height="90" viewBox="0 0 90 90" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M34.364 6.85053C38.6205 -2.28351 51.3795 -2.28351 55.636 6.85053C58.0129 11.951 63.5594 14.6722 68.9556 13.3853C78.6192 11.0807 86.5743 21.2433 82.2185 30.3287C79.7862 35.402 81.1561 41.5165 85.5082 45.0122C93.3019 51.2725 90.4628 63.9451 80.7747 66.1403C75.3648 67.3661 71.5265 72.2695 71.5572 77.9156C71.6123 88.0265 60.1169 93.6664 52.3918 87.3184C48.0781 83.7737 41.9219 83.7737 37.6082 87.3184C29.8831 93.6664 18.3877 88.0266 18.4428 77.9156C18.4735 72.2695 14.6352 67.3661 9.22531 66.1403C-0.462787 63.9451 -3.30193 51.2725 4.49185 45.0122C8.84391 41.5165 10.2138 35.402 7.78151 30.3287C3.42572 21.2433 11.3808 11.0807 21.0444 13.3853C26.4406 14.6722 31.9871 11.951 34.364 6.85053Z"
                            fill="currentColor" />
                    </svg>

                    <span class="absolute -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2">
                        <svg class="text-error-600" width="38" height="38" viewBox="0 0 38 38" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.62684 11.7496C9.04105 11.1638 9.04105 10.2141 9.62684 9.6283C10.2126 9.04252 11.1624 9.04252 11.7482 9.6283L18.9985 16.8786L26.2485 9.62851C26.8343 9.04273 27.7841 9.04273 28.3699 9.62851C28.9556 10.2143 28.9556 11.164 28.3699 11.7498L21.1198 18.9999L28.3699 26.25C28.9556 26.8358 28.9556 27.7855 28.3699 28.3713C27.7841 28.9571 26.8343 28.9571 26.2485 28.3713L18.9985 21.1212L11.7482 28.3715C11.1624 28.9573 10.2126 28.9573 9.62684 28.3715C9.04105 27.7857 9.04105 26.836 9.62684 26.2502L16.8771 18.9999L9.62684 11.7496Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </div>

                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90 sm:text-title-sm">
                     'خطأ!'
                </h4>
                <p  x-text="errorMessage" class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                </p>

                <div class="flex justify-center mt-6">
                    <button @click="showErrorModal = false" type="button"
                        class="px-6 py-2 text-sm font-medium text-error-500 rounded-lg bg-error-500/[0.08] hover:bg-error-500 hover:text-white transition-colors duration-200">
                    'حسناً' 
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =================== مودال عرض السعر الاحترافي =================== -->
<div x-show="showPriceModal" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
    style="display: none;">

    {{-- البطاقة الرئيسية للمودال --}}
    <div @click.away="showPriceModal = false"
        class="relative w-full max-w-[630px]  border border-brand-500 dark:border-brand-500 rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

        {{-- العنوان --}}
        <h3 class="mb-6 text-2xl font-bold text-center text-gray-800 dark:text-white">ملخص التسعيرة</h3>

        {{-- قسم التفاصيل الأساسية (المركبة والمسافة) --}}
        <div class="p-4 mb-6 border border-gray-200 rounded-lg dark:border-gray-700">
            <div class="flex items-center justify-between py-3">
                <span class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                    <i class="w-5 text-center fas fa-car fa-fw"></i>
                    <span>المركبة</span>
                </span>
                {{-- سيتم ملء هذا الحقل من خلال JavaScript --}}
                <span x-text="vehicle ? vehicle : '--'" class="font-semibold text-gray-800 dark:text-white">--</span>
            </div>

            <div class="flex items-center justify-between py-3 border-t border-gray-200 dark:border-gray-700">
                <span class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                    <i class="w-5 text-center fas fa-route fa-fw"></i>
                    <span>المسافة</span>
                </span>
                {{-- سيتم ملء هذا الحقل من خلال JavaScript --}}
                <span class="font-semibold text-gray-800 dark:text-white"
                    x-text="distanceInKm ? distanceInKm + ' كم' : '--'"></span>
            </div>
        </div>

        {{-- قسم تفاصيل السعر --}}
        <div class="mb-8 space-y-3">

            <div class="flex justify-between text-gray-700 dark:text-gray-300">
                <span  class="flex items-center gap-3 text-gray-600 dark:text-gray-400">السعر الأصلي</span>

                <span :class="{ 'line-through text-gray-500': discount_amount > 0 }"
                    x-text="original_price ? original_price + ' ريال' : '...'">
                </span>
            </div>

            <div x-show="discount_amount > 0"
                class="flex items-center justify-between text-green-600 dark:text-green-500">
                <span class="flex items-center gap-2">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="font-semibold text-gray-800 dark:text-white" x-text="'خصم (' + coupon + ') %'"></span>
                </span>
                <span class="font-semibold text-gray-800 dark:text-white"
                    x-text="'- ' + discount_amount + ' ريال'"></span>
            </div>
        </div>

        <!-- الخط الفاصل -->
        <hr class="my-6 border-dashed border-gray-300 dark:border-gray-600">

        <!-- السعر الإجمالي والنهائي -->
        <div class="flex items-center justify-between mb-8">
            <span class="text-lg font-bold text-gray-800 dark:text-white">السعر النهائي</span>
            <span class="text-4xl font-bold text-brand-500"
                x-text="calculatedPrice ? calculatedPrice + ' ريال' : '0 ريال'"></span>
        </div>

        {{-- زر الإغلاق --}}
        <button @click="showPriceModal = false" type="button"
            class="flex justify-center w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
            موافق
        </button>
    </div>
</div>