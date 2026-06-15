<div x-data="{
        activeSection: 'general',
        modalOpen: @entangle('modalOpen'),
        isSuccessModalOpen: false,
        successMessage: ''
    }"
    x-on:notify.window="
        successMessage = $event.detail.message || $event.detail[0]?.message || 'تمت العملية بنجاح';
        isSuccessModalOpen = true;
    ">

    <!-- Success Modal (Same design as x-modals.success-modal) -->
    <div x-show="isSuccessModalOpen" x-cloak class="fixed inset-0 flex items-center justify-center p-5 z-99999"
        x-transition>
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900 shadow-xl">
            <div class="text-center py-4">
                <div class="relative flex items-center justify-center z-1 mb-7">
                    <svg style="color:#d1fae5" width="90" height="90" viewBox="0 0 90 90" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M34.364 6.85053C38.6205 -2.28351 51.3795 -2.28351 55.636 6.85053C58.0129 11.951 63.5594 14.6722 68.9556 13.3853C78.6192 11.0807 86.5743 21.2433 82.2185 30.3287C79.7862 35.402 81.1561 41.5165 85.5082 45.0122C93.3019 51.2725 90.4628 63.9451 80.7747 66.1403C75.3648 67.3661 71.5265 72.2695 71.5572 77.9156C71.6123 88.0265 60.1169 93.6664 52.3918 87.3184C48.0781 83.7737 41.9219 83.7737 37.6082 87.3184C29.8831 93.6664 18.3877 88.0266 18.4428 77.9156C18.4735 72.2695 14.6352 67.3661 9.22531 66.1403C-0.462787 63.9451 -3.30193 51.2725 4.49185 45.0122C8.84391 41.5165 10.2138 35.402 7.78151 30.3287C3.42572 21.2433 11.3808 11.0807 21.0444 13.3853C26.4406 14.6722 31.9871 11.951 34.364 6.85053Z"
                            fill="currentColor" />
                    </svg>

                    <span class="absolute -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2">
                        <svg class="text-success-600" width="38" height="38" viewBox="0 0 38 38" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.9375 19.0004C5.9375 11.7854 11.7864 5.93652 19.0014 5.93652C26.2164 5.93652 32.0653 11.7854 32.0653 19.0004C32.0653 26.2154 26.2164 32.0643 19.0014 32.0643C11.7864 32.0643 5.9375 26.2154 5.9375 19.0004ZM19.0014 2.93652C10.1296 2.93652 2.9375 10.1286 2.9375 19.0004C2.9375 27.8723 10.1296 35.0643 19.0014 35.0643C27.8733 35.0643 35.0653 27.8723 35.0653 19.0004C35.0653 10.1286 27.8733 2.93652 19.0014 2.93652ZM24.7855 17.0575C25.3713 16.4717 25.3713 15.522 24.7855 14.9362C24.1997 14.3504 23.25 14.3504 22.6642 14.9362L17.7177 19.8827L15.3387 17.5037C14.7529 16.9179 13.8031 16.9179 13.2173 17.5037C12.6316 18.0894 12.6316 19.0392 13.2173 19.625L16.657 23.0647C16.9383 23.346 17.3199 23.504 17.7177 23.504C18.1155 23.504 18.4971 23.346 18.7784 23.0647L24.7855 17.0575Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </div>

                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90 sm:text-title-sm">
                    نجاح!
                </h4>
                <p class="text-sm leading-6 text-gray-500 dark:text-gray-400" x-text="successMessage"></p>

                <div class="flex justify-center mt-6">
                    <button @click="isSuccessModalOpen = false" type="button"
                        class="px-6 py-2 text-sm font-medium text-success-500 rounded-lg bg-success-500/[0.08] hover:bg-success-500 hover:text-white transition-colors duration-200">
                        حسناً
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col md:flex-row items-stretch gap-6">

        <!-- Sidebar Navigation (Handled by Alpine.js) -->
        <div
            class="flex-shrink-0 w-full md:w-[250px] rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="sticky top-6">
                <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">أقسام الإعدادات</h3>
                </div>
                <nav class="p-2 flex flex-row overflow-x-auto md:flex-col md:overflow-visible gap-2 no-scrollbar">
                    <button @click="activeSection = 'general'"
                        :class="activeSection === 'general' ? 'menu-item-active' : 'menu-item-inactive'"
                        class="flex w-auto md:w-full flex-shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors whitespace-nowrap">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        الإعدادات العامة
                    </button>
                    <button @click="activeSection = 'surcharges'"
                        :class="activeSection === 'surcharges' ? 'menu-item-active' : 'menu-item-inactive'"
                        class="flex w-auto md:w-full flex-shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors whitespace-nowrap">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        قواعد التسعير
                    </button>
                    @if (Auth::user()->type == 'superAdmin')
                        <button @click="activeSection = 'special'"
                            :class="activeSection === 'special' ? 'menu-item-active' : 'menu-item-inactive'"
                            class="flex w-auto md:w-full flex-shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-base font-medium transition-colors whitespace-nowrap">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                            </svg>
                            الإعدادات الخاصة
                        </button>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 relative">

            <!-- General Settings Section -->
            <div x-show="activeSection === 'general'" x-transition
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">الإعدادات العامة</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إدارة إعدادات التطبيق الأساسية</p>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Commission Rate -->
                    <div class="px-6 py-5 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white">نسبة العمولة</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">النسبة المئوية للعمولة على كل عملية
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span
                                class="text-xl font-bold text-gray-800 dark:text-white">{{ $settings['commission_rate'] }}%</span>
                            <button
                                wire:click="openSimpleModal('تعديل نسبة العمولة', 'يرجى إدخال النسبة المئوية الجديدة للعمولة', 'نسبة العمولة (%)', 'commission_rate', 'number')"
                                wire:loading.attr="disabled"
                                wire:target="openSimpleModal('تعديل نسبة العمولة', 'يرجى إدخال النسبة المئوية الجديدة للعمولة', 'نسبة العمولة (%)', 'commission_rate', 'number')"
                                class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                <svg wire:loading.remove
                                    wire:target="openSimpleModal('تعديل نسبة العمولة', 'يرجى إدخال النسبة المئوية الجديدة للعمولة', 'نسبة العمولة (%)', 'commission_rate', 'number')"
                                    class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <svg wire:loading
                                    wire:target="openSimpleModal('تعديل نسبة العمولة', 'يرجى إدخال النسبة المئوية الجديدة للعمولة', 'نسبة العمولة (%)', 'commission_rate', 'number')"
                                    class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Auto Assign -->
                    <div class="px-6 py-5 flex items-center justify-between text-right">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white">التوزيع التلقائي
                                    للسائقين</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">تفعيل التوزيع التلقائي للطلبات على
                                    السائقين</p>
                            </div>
                        </div>
                        <label class="flex cursor-pointer items-center gap-3">
                            <div class="relative">
                                <input type="checkbox" class="sr-only" wire:model.live="settings.auto_assign">
                                <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                    :class="$wire.settings.auto_assign ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                </div>
                                <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                                    :class="{ 'translate-x-full': $wire.settings.auto_assign }"></div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Surcharges Section -->
            <div x-show="activeSection === 'surcharges'" x-transition
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">قواعد التسعير الإضافية
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إدارة الزيادات السعرية حسب الوقت
                                والظروف</p>
                        </div>

                        {{-- <button wire:click="openSurchargeModal()"
                            class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-all">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة قاعدة جديدة
                        </button> --}}
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="max-w-full overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr
                                        class="bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            اسم القاعدة</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            من الساعة</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            إلى الساعة</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            المبلغ</th>
                                        <th
                                            class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            الحالة</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-transparent divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($surcharges as $surcharge)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition-colors">
                                            <td
                                                class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $surcharge->name }}
                                            </td>
                                            <td
                                                class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                {{ $surcharge->time_from ? \Carbon\Carbon::parse($surcharge->time_from)->translatedFormat('g:i a') : '-' }}
                                            </td>
                                            <td
                                                class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                {{ $surcharge->time_to ? \Carbon\Carbon::parse($surcharge->time_to)->translatedFormat('g:i a') : '-' }}
                                            </td>
                                            <td
                                                class="px-5 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $surcharge->amount }}
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <label class="flex cursor-pointer items-center">
                                                    <div class="relative">
                                                        <input type="checkbox" class="sr-only"
                                                            wire:click="toggleSurcharge({{ $surcharge->id }})" {{ $surcharge->is_active ? 'checked' : '' }}>
                                                        <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                                            :class="{{ $surcharge->is_active ? 'true' : 'false' }} ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                                        </div>
                                                        <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                                                            :class="{ 'translate-x-full': {{ $surcharge->is_active ? 'true' : 'false' }} }">
                                                        </div>
                                                    </div>
                                                </label>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-sm text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button wire:click="openSurchargeModal({{ $surcharge->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                                        <svg wire:loading.remove
                                                            wire:target="openSurchargeModal({{ $surcharge->id }})"
                                                            class="h-5 w-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        <svg wire:loading
                                                            wire:target="openSurchargeModal({{ $surcharge->id }})"
                                                            class="animate-spin h-5 w-5 text-brand-500" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">
                                                لا توجد قواعد تسعير حالياً</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if (Auth::user()->type == 'superAdmin')
                <!-- Special Settings Section -->
                <div x-show="activeSection === 'special'" x-transition
                    class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">الإعدادات الخاصة</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تخصيص الخيارات المتقدمة وروابط التطبيق</p>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        <!-- WhatsApp Support -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">رقم الواتساب</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">رقم الدعم الفني الخاص بالتطبيق</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-lg font-medium text-gray-800 dark:text-white">{{ $settings['whatsapp_support'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل رقم الواتساب', 'يرجى إدخال رقم الواتساب الجديد للدعم الفني', 'رقم الواتساب', 'whatsapp_support', 'text')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل رقم الواتساب', 'يرجى إدخال رقم الواتساب الجديد للدعم الفني', 'رقم الواتساب', 'whatsapp_support', 'text')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل رقم الواتساب', 'يرجى إدخال رقم الواتساب الجديد للدعم الفني', 'رقم الواتساب', 'whatsapp_support', 'text')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل رقم الواتساب', 'يرجى إدخال رقم الواتساب الجديد للدعم الفني', 'رقم الواتساب', 'whatsapp_support', 'text')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Company Website -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">الموقع الإلكتروني</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">رابط الموقع الرسمي للشركة</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white truncate max-w-[200px]"
                                    dir="ltr">{{ $settings['company_website'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل الموقع الإلكتروني', 'يرجى إدخال رابط الموقع الرسمي للشركة', 'رابط الموقع', 'company_website', 'url')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل الموقع الإلكتروني', 'يرجى إدخال رابط الموقع الرسمي للشركة', 'رابط الموقع', 'company_website', 'url')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل الموقع الإلكتروني', 'يرجى إدخال رابط الموقع الرسمي للشركة', 'رابط الموقع', 'company_website', 'url')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل الموقع الإلكتروني', 'يرجى إدخال رابط الموقع الرسمي للشركة', 'رابط الموقع', 'company_website', 'url')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Company Email -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">البريد الإلكتروني للشركة</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">البريد الإلكتروني الرسمي للشركة</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white truncate max-w-[200px]"
                                    dir="ltr">{{ $settings['company_email'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل البريد الإلكتروني', 'يرجى إدخال البريد الإلكتروني للشركة', 'البريد الإلكتروني', 'company_email', 'email')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل البريد الإلكتروني', 'يرجى إدخال البريد الإلكتروني للشركة', 'البريد الإلكتروني', 'company_email', 'email')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل البريد الإلكتروني', 'يرجى إدخال البريد الإلكتروني للشركة', 'البريد الإلكتروني', 'company_email', 'email')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل البريد الإلكتروني', 'يرجى إدخال البريد الإلكتروني للشركة', 'البريد الإلكتروني', 'company_email', 'email')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Android Update URL -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">رابط تحديث أندرويد</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">الرابط المباشر لتحديث تطبيق أندرويد</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white truncate max-w-[200px]"
                                    dir="ltr">{{ $settings['android_update_url'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل رابط تحديث أندرويد', 'يرجى إدخال الرابط المباشر لتحديث تطبيق أندرويد', 'رابط تحديث أندرويد', 'android_update_url', 'url')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل رابط تحديث أندرويد', 'يرجى إدخال الرابط المباشر لتحديث تطبيق أندرويد', 'رابط تحديث أندرويد', 'android_update_url', 'url')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل رابط تحديث أندرويد', 'يرجى إدخال الرابط المباشر لتحديث تطبيق أندرويد', 'رابط تحديث أندرويد', 'android_update_url', 'url')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل رابط تحديث أندرويد', 'يرجى إدخال الرابط المباشر لتحديث تطبيق أندرويد', 'رابط تحديث أندرويد', 'android_update_url', 'url')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- iOS Update URL -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">رابط تحديث iOS</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">الرابط المباشر لتحديث تطبيق iOS</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white truncate max-w-[200px]"
                                    dir="ltr">{{ $settings['ios_update_url'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل رابط تحديث iOS', 'يرجى إدخال الرابط المباشر لتحديث تطبيق iOS', 'رابط تحديث iOS', 'ios_update_url', 'url')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل رابط تحديث iOS', 'يرجى إدخال الرابط المباشر لتحديث تطبيق iOS', 'رابط تحديث iOS', 'ios_update_url', 'url')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل رابط تحديث iOS', 'يرجى إدخال الرابط المباشر لتحديث تطبيق iOS', 'رابط تحديث iOS', 'ios_update_url', 'url')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل رابط تحديث iOS', 'يرجى إدخال الرابط المباشر لتحديث تطبيق iOS', 'رابط تحديث iOS', 'ios_update_url', 'url')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- App Version -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">إصدار التطبيق</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">رقم الإصدار الحالي المنشور</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-lg font-bold text-gray-800 dark:text-white">{{ $settings['app_version'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل رقم الإصدار', 'يرجى إدخال رقم الإصدار الحالي للتطبيق', 'رقم الإصدار', 'app_version', 'text')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل رقم الإصدار', 'يرجى إدخال رقم الإصدار الحالي للتطبيق', 'رقم الإصدار', 'app_version', 'text')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل رقم الإصدار', 'يرجى إدخال رقم الإصدار الحالي للتطبيق', 'رقم الإصدار', 'app_version', 'text')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل رقم الإصدار', 'يرجى إدخال رقم الإصدار الحالي للتطبيق', 'رقم الإصدار', 'app_version', 'text')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Reference Number -->
                        <div class="px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">الرقم المرجعي (Ref No)
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">الرقم المرجعي الخاص بالنظام</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-lg font-bold text-gray-800 dark:text-white">{{ $settings['ref_no'] }}</span>
                                <button
                                    wire:click="openSimpleModal('تعديل الرقم المرجعي', 'يرجى إدخال الرقم المرجعي الجديد للنظام', 'الرقم المرجعي', 'ref_no', 'text')"
                                    wire:loading.attr="disabled"
                                    wire:target="openSimpleModal('تعديل الرقم المرجعي', 'يرجى إدخال الرقم المرجعي الجديد للنظام', 'الرقم المرجعي', 'ref_no', 'text')"
                                    class="p-1 text-gray-400 hover:text-brand-500 transition-colors relative">
                                    <svg wire:loading.remove
                                        wire:target="openSimpleModal('تعديل الرقم المرجعي', 'يرجى إدخال الرقم المرجعي الجديد للنظام', 'الرقم المرجعي', 'ref_no', 'text')"
                                        class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg wire:loading
                                        wire:target="openSimpleModal('تعديل الرقم المرجعي', 'يرجى إدخال الرقم المرجعي الجديد للنظام', 'الرقم المرجعي', 'ref_no', 'text')"
                                        class="animate-spin h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Maintenance Mode -->
                        <div class="px-6 py-5 flex items-center justify-between text-right">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">وضع الصيانة</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">إغلاق التطبيق للصيانة المؤقتة</p>
                                </div>
                            </div>
                            <label class="flex cursor-pointer items-center gap-3">
                                <div class="relative">
                                    <input type="checkbox" class="sr-only" wire:model.live="settings.maintenance_mode">
                                    <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                        :class="$wire.settings.maintenance_mode ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                    </div>
                                    <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                                        :class="{ 'translate-x-full': $wire.settings.maintenance_mode }"></div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- OTP Enabled Toggle -->
                        <div class="px-6 py-5 flex items-center justify-between text-right">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 menu-item-icon-active items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800">
                                    <!-- أيقونة قفل الحماية للـ OTP -->
                                    <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">التحقق برمز الـ OTP
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">تفعيل أو إيقاف التحقق من أرقام
                                        الهواتف (للطوارئ)</p>
                                </div>
                            </div>
                            <label class="flex cursor-pointer items-center gap-3">
                                <div class="relative">
                                    <input type="checkbox" class="sr-only" wire:model.live="settings.otp_enabled">
                                    <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                        :class="$wire.settings.otp_enabled ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                    </div>
                                    <div class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                                        :class="{ 'translate-x-full': $wire.settings.otp_enabled }"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Centralized Modal (Managed by Alpine + Livewire) -->
        <div x-show="modalOpen" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-99999 flex items-center justify-center p-4 overflow-y-auto modal">

            <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="modalOpen = false">
            </div>

            <div @click.outside="modalOpen = false"
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-300 delay-75"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md p-6 bg-white rounded-2xl shadow-xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 z-10">

                <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $modalTitle }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $modalDesc }}</p>
                    </div>
                    <button @click="modalOpen = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Simple Input Form -->
                @if($modalType === 'simple')
                    <div class="space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $simpleLabel }}</label>
                            <input type="{{ $simpleInputType }}" wire:model="simpleValue"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-all outline-none"
                                @if($simpleInputType === 'number') step="any" @endif>
                            @error('simpleValue') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <!-- Surcharge Form -->
                @if($modalType === 'surcharge')
                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">اسم القاعدة</label>
                            <input type="text" wire:model="surchargeForm.name"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-all outline-none"
                                placeholder="مثال: زيادة الفترة الليلية">
                            @error('surchargeForm.name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if($isTimeEditable)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">من
                                        الساعة</label>
                                    <input type="time" wire:model="surchargeForm.time_from"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">إلى
                                        الساعة</label>
                                    <input type="time" wire:model="surchargeForm.time_to"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white outline-none">
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">المبلغ /
                                    القيمة</label>
                                <input type="number" wire:model="surchargeForm.amount"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-all outline-none">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Modal Footer -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-800 mt-6">
                    <button @click="modalOpen = false"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white transition-all">
                        إلغاء
                    </button>
                    <button wire:click="saveSettings()" wire:loading.attr="disabled" wire:target="saveSettings"
                        class="flex-1 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 shadow-md shadow-brand-500/20 transition-all disabled:opacity-70">
                        <span wire:loading.remove wire:target="saveSettings">حفظ التغييرات</span>
                        <span wire:loading wire:target="saveSettings" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            جاري الحفظ...
                        </span>
                    </button>
                </div>
            </div>
        </div>
</div>