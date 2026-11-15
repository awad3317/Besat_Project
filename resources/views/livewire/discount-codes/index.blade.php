<div class="p-4 md:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit" dir="rtl">
    <style>
        .stats-three-cols {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
        }
        @media (min-width: 640px) {
            .stats-three-cols {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }
        .coupons-three-cols {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
        }
        @media (min-width: 640px) {
            .coupons-three-cols {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .coupons-three-cols {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }
    </style>

    <div class="max-w-[1400px] mx-auto space-y-6">

        <!-- Top Stats Cards (صف من 3 أعمدة) -->
        <div class="stats-three-cols mb-6">

            <!-- Total Coupons Card -->
            <div wire:click="applyFilter('all')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md
                @if($filter == 'all') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="applyFilter('all')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                {{-- Icon --}}
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="26" height="26" fill="none" stroke="#dc6803" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">إجمالي الكوبونات</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] }}</h4>
                </div>
            </div>

            <!-- Active Coupons Card -->
            <div wire:click="applyFilter('active')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md
                @if($filter == 'active') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="applyFilter('active')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg fill="#dc6803" height="26" width="26" viewBox="0 0 24 24" stroke="#dc6803" stroke-width="0.5">
                        <path d="M8.6,20.1l-7.8-8l1.4-1.4l6.4,6.5L21.8,3.9l1.4,1.4L8.6,20.1z"></path>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">الكوبونات النشطة</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['active'] }}</h4>
                </div>
            </div>

            <!-- Inactive Coupons Card -->
            <div wire:click="applyFilter('inactive')" wire:loading.class="opacity-50"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-5 dark:bg-white/[0.03] transition hover:shadow-md
                @if($filter == 'inactive') border border-brand-500 dark:border-brand-500 @else border border-gray-100 dark:border-gray-800 @endif">

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="applyFilter('inactive')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                {{-- Icon --}}
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="26" height="26" viewBox="0 0 512 512" fill="#dc6803">
                        <path d="M256,48C141.1,48,48,141.1,48,256s93.1,208,208,208,208-93.1,208-208S370.9,48,256,48Zm0,374.4c-91.8,0-166.4-74.6-166.4-166.4,0-38.6,13.2-74.2,35.2-102.5l233.7,233.7c-28.3,22-63.9,35.2-102.5,35.2Zm131.2-63.9L153.5,124.8c28.3-22,63.9-35.2,102.5-35.2,91.8,0,166.4,74.6,166.4,166.4,0,38.6-13.2,74.2-35.2,102.3Z" />
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-4 w-full">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">الكوبونات المنتهية</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $this->stats['inactive'] }}</h4>
                </div>
            </div>

        </div>

        <!-- Main Card with Search and Coupons List -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

            <!-- Search Bar -->
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="flex-1 w-full">
                        <label class="block mb-2 font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            ابحث عن كوبون
                        </label>
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="search" type="text"
                                class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 text-right"
                                placeholder="اكتب كود الكوبون هنا..." />
                            <div class="absolute left-0 top-0 bottom-0 flex items-center px-3">
                                <div wire:loading wire:target="search">
                                    <div class="h-5 w-5 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Body: Coupons Grid (3 أعمدة في الصف) -->
            <div class="p-5 sm:p-6">
                <div wire:loading.class.delay="opacity-50"
                    wire:target="search, nextPage, previousPage, gotoPage"
                    class="coupons-three-cols transition-opacity">

                    @forelse ($this->coupons as $coupon)
                    <div wire:key="coupon-{{ $coupon->id }}"
                        class="group bg-white dark:bg-gray-dark dark:border-gray-700 rounded-xl border border-gray-200 shadow-theme-sm hover:shadow-theme-md transition-all duration-300 overflow-visible flex flex-col justify-between">

                        <!-- 1. Card Header -->
                        <div class="p-5 sm:p-6 flex-grow">
                            <div class="flex items-start justify-between gap-4 text-right">
                                {{-- Icon & Info --}}
                                <div class="flex items-center gap-4 min-w-0">
                                    <div
                                        class="h-12 w-12 sm:h-14 sm:w-14 flex-shrink-0 flex items-center justify-center rounded-xl border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                        </svg>
                                    </div>
                                    <div class="space-y-1.5 min-w-0">
                                        <h4 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                            {{ $coupon->code }}
                                        </h4>

                                        <p class="text-xs text-gray-500 font-medium">معدل الخصم:
                                            <span class="font-bold text-brand-600 dark:text-brand-400">{{ number_format($coupon->discount_rate * 100, 2) }}%</span>
                                        </p>
                                        
                                        <p class="text-xs text-gray-400 font-semibold">استخدم
                                            <span class="text-gray-700 dark:text-gray-300 font-bold">{{ $coupon->current_uses }}</span> / {{ $coupon->max_uses }} مرة
                                        </p>
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex-shrink-0">
                                    <span
                                        class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm @if($coupon->is_active) bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400 @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                        {{ $coupon->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Card Footer -->
                        <div
                            class="px-5 sm:px-6 py-4 bg-gray-50/50 dark:bg-white/[0.03] border-t border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4 rounded-b-xl">
                            {{-- Progress Bar (Usage Line) --}}
                            <div class="flex-1 min-w-0">
                                @php($percentage = $coupon->max_uses > 0 ? min(100, ($coupon->current_uses / $coupon->max_uses) * 100) : 0)
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">الاستخدام</span>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ number_format($percentage, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 overflow-hidden">
                                    <div class="bg-brand-500 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>

                            {{-- Action Dropdown Buttons --}}
                            <div class="flex relative items-center justify-center flex-shrink-0" x-data="{ openOptions: false }">
                                <button @click="openOptions = !openOptions" @click.away="openOptions = false"
                                    class="actions-trigger-btn">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="2"></circle>
                                        <circle cx="12" cy="12" r="2"></circle>
                                        <circle cx="12" cy="19" r="2"></circle>
                                    </svg>
                                </button>

                                <div x-show="openOptions" x-transition.opacity.duration.200ms x-cloak
                                    class="actions-dropdown-menu">

                                    <!-- عرض التفاصيل -->
                                    <a href="{{ route('Coupon.show', $coupon->id) }}"
                                        class="actions-dropdown-item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        عرض التفاصيل
                                    </a>

                                    <!-- تعديل -->
                                    <a href="{{ route('Coupon.edit', $coupon->id) }}"
                                        class="actions-dropdown-item edit-data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        تعديل الكوبون
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-16">
                        <p class="text-lg font-medium text-gray-500">لا توجد كوبونات تطابق بحثك.</p>
                        <p class="text-sm text-gray-400 mt-2">جرّب تغيير كلمة البحث أو الفلتر.</p>
                    </div>
                    @endforelse
                </div>

                @if ($this->coupons->hasPages())
                    <div class="mt-6">
                        {{ $this->coupons->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
