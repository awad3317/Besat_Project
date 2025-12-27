<!-- =================== مودال عرض السعر الاحترافي =================== -->
<div x-show="showPriceModal" 
     class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
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
                <span  x-text="distanceInKm ? distanceInKm + ' كم' : '--'"></span>
            </div>
        </div>

        {{-- قسم تفاصيل السعر --}}
        <div class="mb-8 space-y-3">
            <!-- السعر الأصلي -->
            <div class="flex justify-between text-gray-700 dark:text-gray-300">
                <span>السعر الأصلي</span>
                {{-- يظهر خط في منتصف السعر عند وجود خصم --}}
                <span :class="{ 'line-through text-gray-500': discount_amount > 0 }" 
                      x-text="original_price ? original_price + ' ريال' : '...'">
                </span>
            </div>

            <!-- تفاصيل الخصم (تظهر عند وجود خصم فقط) -->
            <div x-show="discount_amount > 0" class="flex items-center justify-between text-green-600 dark:text-green-500">
                <span class="flex items-center gap-2">
                    <i class="fas fa-ticket-alt"></i>
                    <span x-text="'خصم (' + coupon + ') %'"></span>
                </span>
                <span x-text="'- ' + discount_amount + ' ريال'"></span>
            </div>
        </div>

        <!-- الخط الفاصل -->
        <hr class="my-6 border-dashed border-gray-300 dark:border-gray-600">

        <!-- السعر الإجمالي والنهائي -->
        <div class="flex items-center justify-between mb-8">
            <span class="text-lg font-bold text-gray-800 dark:text-white">السعر النهائي</span>
            <span class="text-4xl font-bold text-brand-500" x-text="calculatedPrice ? calculatedPrice + ' ريال' : '...'"></span>
        </div>

        {{-- زر الإغلاق --}}
        <button @click="showPriceModal = false" type="button"
                class="flex justify-center w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
            موافق
        </button>
    </div>
</div>
