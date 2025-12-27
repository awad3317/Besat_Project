<div x-data="{ isModalOpen: false }">
    <button @click="isModalOpen = true"
        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 py-2.5 px-6 text-sm font-bold text-white hover:bg-brand-600 shadow-theme-xs transition-all active:scale-95">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14" />
        </svg>
        <span>كوبون جديد</span>
    </button>

    <div x-show="isModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
        style="display: none;">
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
        </div>

        <div @click.outside="isModalOpen = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

            <form method="POST" action="{{ route('Coupon.store') }}">
                @csrf
                <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90">
                    إضافة كوبون خصم جديد
                </h4>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                    <div class="sm:col-span-2">
                        <label for="code" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            كود الخصم <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="text" id="code" name="code" required placeholder="مثال: RAMADAN25"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                            <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">
                                يجب أن يكون فريد (غير مكرر)
                        </p>
                    </div>

                    <div>
                        <label for="discount_rate"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            معدل الخصم (%) <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="number" id="discount_rate" name="discount_rate" required step="0.01" min="0.01"
                            max="100" placeholder="مثال: 15.5"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">أدخل النسبة المئوية للخصم (مثل 10
                            أو 25.5).</p>
                    </div>
                    
                    <div>
                        <label for="max_uses" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            الحد الأقصى للاستخدام <span
                                class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="number" id="max_uses" name="max_uses" required min="1" placeholder="مثال: 100"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="usage_limit_per_user" 
                               class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            الحد الأقصى لكل مستخدم <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                        </label>
                        <input type="number" id="usage_limit_per_user" name="usage_limit_per_user" 
                               min="1" placeholder="مثال: 3"
                               class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">
                            عدد المرات التي يستطيع كل مستخدم استخدام هذا الكوبون (اتركه فارغاً ليكون غير محدود)
                        </p>
                    </div>
                    
                    <div class="sm:col-span-2">
                        {{-- 1. Initialize Alpine.js component. 'isActive' starts as true. --}}
                        <div x-data="{ isActive: true }">
                            {{-- استبدال label بـ div لتجنب تضارب الأحداث، والتحكم بالحالة عبر النقر على الحاوية --}}
                            <div @click="isActive = !isActive"
                                class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                                {{-- حقل مخفي واحد يحمل القيمة بناءً على الحالة --}}
                                <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                                <div class="relative">

                                    {{-- Background of the switch --}}
                                    {{-- :class changes the color based on 'isActive' state --}}
                                    <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                        :class="isActive ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                    </div>

                                    {{-- The sliding circle --}}
                                    {{-- :class moves the circle based on 'isActive' state --}}
                                    <div class="shadow-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white transition-transform duration-300 ease-in-out"
                                        :class="{ 'translate-x-full': isActive }">
                                    </div>
                                </div>

                                <span>تفعيل الكوبون  </span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="isModalOpen = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                        إغلاق
                    </button>
                    <button type="submit"
                        class="flex justify-center hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
                        إنشاء الكوبون
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>