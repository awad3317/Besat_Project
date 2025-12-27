<div class="p-4 md:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit" dir="rtl">
    <div class="max-w-[1400px] mx-auto space-y-6">

        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-1">

            <!-- Total Coupons Card -->
            <div wire:click="applyFilter('all')" wire:loading.class="opacity-50" class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'all') border border-brand-500 dark:border-brand-500 @endif">

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="applyFilter('all')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                {{-- Icon --}}
                <div
                    class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الكوبونات</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] }}</h4>
                </div>
            </div>

            <!-- Active Coupons Card -->
            <div wire:click="applyFilter('active')" class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'active') border border-brand-500 dark:border-brand-500 @endif">

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="applyFilter('active')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent">
                    </div>
                </div>
                <div
                    class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg fill="#dc6803" height="30" width="30" version="1.1"
                        xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" xml:space="preserve"
                        stroke="#dc6803" stroke-width="0.00024000000000000003">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g id="active">
                                <path d="M8.6,20.1l-7.8-8l1.4-1.4l6.4,6.5L21.8,3.9l1.4,1.4L8.6,20.1z"></path>
                            </g>
                        </g>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">الكوبونات النشطة</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['active'] }}
                    </h4>
                </div>
            </div>

            <!-- Inactive Coupons Card -->
            <div wire:click="applyFilter('inactive')" class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]
                @if($filter == 'inactive') border border-brand-500 dark:border-brand-500 @endif">

                {{-- Loading Spinner --}}
                <div wire:loading wire:target="applyFilter('inactive')"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 rounded-xl z-10">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-brand-500 border-t-transparent"></div>
                </div>

                {{-- Icon --}}
                <div
                    class="flex h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg width="30" height="30" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" fill="#dc6803" stroke="#dc6803">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <title>cancelled</title>
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="add" fill="#dc6803" transform="translate(42.666667, 42.666667)">
                                    <path
                                        d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z"
                                        id="Combined-Shape"> </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>

                {{-- Content --}}
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">الكوبونات المنتهية</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['inactive'] }}
                    </h4>
                </div>
            </div>
            <div
                class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>

        </div>
        <div class="pt-4 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-outfit"
            dir="rtl">
            <div class="max-w-[1400px] mx-auto space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex flex-col sm:flex-row gap-4 items-center">
                            <div class="flex-1 w-full">
                                <label class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    ابحث عن كوبون
                                </label>
                                <div class="relative">
                                    <input wire:model.live.debounce.300ms="search" type="text"
                                        class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 text-right"
                                        placeholder="اكتب كود الكوبون هنا..." />
                                    <div class="absolute left-0 top-0 bottom-0 flex items-center px-3">
                                        <div wire:loading wire:target="search" class="mt-1">
                                            <div
                                                class="h-5 w-5 animate-spin rounded-full border-2 border-brand-500 border-t-transparent">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Body -->
                    <div class="p-5 sm:p-6">
                        <!-- Cards Grid -->
                        <!-- Cards Grid -->
                        <div wire:loading.class.delay="opacity-50"
                            wire:target="search, nextPage, previousPage, gotoPage"
                            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 transition-opacity">

                            @forelse ($this->coupons as $coupon)
                            <div wire:key="coupon-{{ $coupon->id }}"
                                class="group bg-white dark:bg-gray-dark dark:border-gray-700 rounded-xl border shadow-theme-sm hover:shadow-theme-md transition-all duration-300 overflow-hidden flex flex-col">

                                <!-- 1. Card Header (Static) -->
                                <div class="p-6 flex-grow">
                                    <div class="flex items-start justify-between text-right">
                                        {{-- Left Side: Icon and Info --}}
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="size-14 flex-shrink-0 flex items-center justify-center rounded-full border-2 border-gray-100 dark:border-gray-700 shadow-theme-xs bg-gray-50 dark:bg-gray-800 text-brand-500">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                                </svg>
                                            </div>
                                            <div class="space-y-1">
                                                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                                    {{ $coupon->code }}
                                                </h4>
                                                
                                                <p class="text-theme-xs text-gray-500 font-medium">معدل الخصم:
                                                    {{ number_format($coupon->discount_rate * 100, 2) }}%
                                                </p>
                                                <p class="text-[11px] text-gray-400 font-semibold">استخدم
                                                    {{ $coupon->current_uses }} / {{ $coupon->max_uses }} مرة
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Right Side: Status Badge --}}
                                        <div class="flex-shrink-0">
                                            <span
                                                class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm @if($coupon->is_active) bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400 @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 @endif">
                                                {{ $coupon->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Card Footer (Static) -->
                                <div
                                    class="px-6 py-4 bg-gray-50/50 dark:bg-white/[0.03] border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                    {{-- Progress Bar --}}
                                    <div class="w-full">
                                        @php($percentage = $coupon->max_uses > 0 ? ($coupon->current_uses / $coupon->max_uses) * 100 : 0)
                                        <div class="flex justify-between mb-1">
                                            <span
                                                class="text-xs font-medium text-gray-600 dark:text-white">الاستخدام</span>
                                            <span
                                                class="text-xs font-medium text-gray-600 dark:text-white">{{ number_format($percentage, 0) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                            <div class="bg-brand-500 h-1.5 rounded-full"
                                                style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-1 ms-4 flex-shrink-0">

                                        <a href="{{ route('Coupon.show', $coupon->id) }}" title="عرض التفاصيل"
                                            class="p-2 text-gray-400 hover:text-sky-500 hover:bg-sky-50 dark:hover:bg-sky-500/10 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- Edit Button --}}
                                        <a href="{{ route('Coupon.edit', $coupon->id) }}" title="تعديل"
                                            class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 rounded-lg transition-colors">
                                            <svg width="18" height="18" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Delete Button --}}
                                        {{-- <button type="button" title="حذف" wire:click="delete({{ $coupon->id }})"
                                            wire:confirm="هل أنت متأكد من حذف الكوبون '{{ $coupon->code }}'؟"
                                            class="p-2 text-gray-400 hover:text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10 rounded-lg transition-colors">
                                            <svg width="18" height="18" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button> --}}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="md:col-span-2 xl:col-span-3 2xl:col-span-4 text-center py-16">
                                <p class="text-lg font-medium text-gray-500">لا توجد كوبونات تطابق بحثك.</p>
                                <p class="text-sm text-gray-400 mt-2">جرّب تغيير كلمة البحث أو الفلتر.</p>
                            </div>
                            @endforelse
                        </div>
                        @if ($this->coupons->hasPages())
                            <div style="margin-top: 20px;">
                                {{ $this->coupons->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>