<div>
    {{-- 1. بطاقات الإحصائيات والفلاتر حسب نوع الخدمة (service_type) --}}
    <div class="flex flex-col flex-wrap gap-4 mb-4 sm:flex-row md:gap-6">
        {{-- فلتر: الكل --}}
        <div wire:click.debounce.150ms="applyFilter('all')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'all') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('all')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg fill="#dc6803" width="24" height="24" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الحجوزات</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['total'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- فلتر: فعالياتنا (events) --}}
        <div wire:click.debounce.150ms="applyFilter('events')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'events') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('events')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">فعالياتنا</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['events'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- فلتر: زفونا (zafona) --}}
        <div wire:click.debounce.150ms="applyFilter('zafona')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'zafona') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('zafona')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">زفونا</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['zafona'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- فلتر: رحلة (rehla) --}}
        <div wire:click.debounce.150ms="applyFilter('rehla')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'rehla') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('rehla')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">رحلة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['rehla'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- فلتر: بالسلامة (safety_airport) --}}
        <div wire:click.debounce.150ms="applyFilter('safety_airport')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'safety_airport') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('safety_airport')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="#dc6803" viewBox="0 0 24 24">
                    <path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 00-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">بالسلامة</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['safety_airport'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- فلتر: دوام (dawam) --}}
        <div wire:click.debounce.150ms="applyFilter('dawam')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'dawam') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('dawam')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">دوام</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['dawam'] ?? 0 }}</h4>
            </div>
        </div>

        {{-- فلتر: مشوارنا (moshwarana) --}}
        <div wire:click.debounce.150ms="applyFilter('moshwarana')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl p-4 transition hover:shadow-md bg-white dark:bg-white/[0.03] flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'moshwarana') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('moshwarana')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">مشوارنا</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $this->stats['moshwarana'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    {{-- ════════════ 2. الحاوية الرئيسية للبحث والجدول (Main Content Card) ════════════ --}}
    <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
        {{-- قسم البحث --}}
        <div class="mb-5 text-right">
            <label class="block mb-1.5 font-medium text-right text-gray-500 text-theme-xs dark:text-gray-400">البحث</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="px-4 py-2.5 w-full h-10 text-sm text-right text-gray-800 bg-transparent rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-orange-500 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="ابحث برقم الحجز، اسم العميل، الهاتف، العنوان، أو نوع المركبة..." />
                <div class="flex absolute top-0 bottom-0 left-0 items-center px-3">
                    <div wire:loading wire:target="search">
                        <div class="w-5 h-5 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- جدول البيانات (Data Table) --}}
        <div wire:loading.class="opacity-50" wire:target="search,applyFilter"
            class="overflow-x-auto max-w-full table-responsive-container">
            <table class="min-w-full">
                <thead>
                    <tr class="border-gray-100 border-y dark:border-gray-800">
                        <th class="px-3 py-3 text-right">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم الحجز / الخدمة</p>
                        </th>
                        <th class="px-3 py-3 text-right">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">العميل</p>
                        </th>
                        <th class="px-3 py-3 text-right">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">تفاصيل الحجز والمركبة</p>
                        </th>
                        <th class="px-3 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">السعر والدفع</p>
                        </th>
                        <th class="px-3 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">حالة الحجز</p>
                        </th>
                        <th class="px-3 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">من - إلى</p>
                        </th>
                        <th class="px-3 py-3 text-center">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">الإجراءات</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->orders as $order)
                        <tr wire:key="service-booking-{{ $order->id }}" class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition">
                            {{-- 1. رقم الحجز ونوع الخدمة --}}
                            <td class="px-3 py-3">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="font-bold text-gray-800 text-theme-sm dark:text-white/90">#{{ $order->id }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                        {{ $order->service_type_text }}
                                    </span>
                                    <span class="text-[11px] text-gray-400" dir="ltr">
                                        {{ $order->created_at?->format('Y-m-d H:i') }}
                                    </span>
                                </div>
                            </td>

                            {{-- 2. العميل --}}
                            <td class="px-3 py-3">
                                <div class="flex gap-3 items-center">
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $order->client_name ?: ($order->user?->name ?? 'عميل غير محدد') }}
                                        </p>
                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400" dir="ltr">
                                            {{ $order->user?->phone ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- 3. تفاصيل الحجز والمركبة --}}
                            <td class="px-3 py-3">
                                <div class="flex flex-col gap-1 text-xs text-gray-600 dark:text-gray-300">
                                    <div class="flex flex-wrap gap-1.5 items-center">
                                        @if ($order->vehicle)
                                            <span class="font-semibold text-gray-800 dark:text-white">{{ $order->vehicle->type }}</span>
                                            <span>•</span>
                                        @endif
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $order->vehicles_count }} مركبة</span>
                                        <span>•</span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400" >{{ $order->passenger_count }} ركاب</span>
                                        <span>•</span>
                                        <span class="{{ $order->wants_ac ? 'text-blue-light-500 font-medium' : 'text-gray-400' }}">
                                            {{ $order->wants_ac ? 'مكيف' : 'بدون تكييف' }}
                                        </span>
                                    </div>

                                    @if ($order->trip_datetime)
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                            الموعد: <span dir="ltr" class="font-medium">{{ $order->trip_datetime->format('Y-m-d H:i') }}</span>
                                            @if ($order->duration)
                                                <span>({{ $order->duration }})</span>
                                            @endif
                                        </div>
                                    @elseif ($order->duration)
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                            المدة: <span class="font-medium">{{ $order->duration }}</span>
                                        </div>
                                    @endif

                                    @php
                                        $details = is_array($order->service_details) ? $order->service_details : [];
                                    @endphp
                                    @if (!empty($details['work_or_school_name']))
                                        <div class="text-[11px] text-brand-600 dark:text-brand-400">
                                            الجهة: {{ $details['work_or_school_name'] }}
                                        </div>
                                    @endif
                                    @if (!empty($details['airport_direction']))
                                        <div class="text-[11px] text-blue-light-500">
                                            {{ $details['airport_direction'] === 'to_airport' ? 'إلى المطار' : 'من المطار' }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- 4. السعر والدفع --}}
                            <td class="px-3 py-3 text-center">
                                <div class="flex flex-col gap-1 items-center">
                                    @if ($order->total_price !== null && $order->total_price > 0)
                                        <span class="font-semibold text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ number_format($order->total_price) }}
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ config('app.currency_symbol', 'ر.ي') }}</span>
                                        </span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $order->payment_status_class }}">
                                            {{ $order->payment_status_text }}
                                        </span>
                                    @else
                                        <button wire:click="openEditModal({{ $order->id }})"
                                            class="inline-flex gap-1 items-center px-2.5 py-0.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-full transition hover:bg-brand-50 hover:text-brand-600 dark:bg-gray-700 dark:text-gray-300">
                                            <span>تحديد السعر</span>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. حالة الحجز --}}
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-theme-xs font-medium {{ $order->status_class }}">
                                    {{ $order->status_text }}
                                </span>
                            </td>

                            {{-- 6. من - إلى --}}
                            <td class="px-3 py-3">
                                <div class="flex flex-col gap-1 items-center">
                                    <div class="flex flex-wrap gap-2 justify-center items-center">
                                        <span class="px-2.5 py-0.5 font-medium rounded-full bg-warning-50 text-theme-xs text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                            {{ $order->start_address ?: 'غير محدد' }}
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                        <span class="px-2.5 py-0.5 font-medium rounded-full text-theme-xs bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                            {{ $order->end_address ?: 'غير محدد' }}
                                        </span>
                                    </div>
                                    @if ($order->stops_count > 0)
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                            ({{ $order->stops_count }} نقاط توقف)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 7. الإجراءات --}}
                            <td class="px-4 py-3 text-center align-middle" x-data="{ openOptions: false }">
                                <div class="flex relative justify-center items-center">
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

                                        {{-- عرض التفاصيل (صفحة كاملة) --}}
                                        <a href="{{ route('specialOrder.show', $order->id) }}"
                                            class="actions-dropdown-item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض التفاصيل
                                        </a>

                                        {{-- تعديل الحجز / التسعيرة --}}
                                        <a href="{{ route('specialOrder.edit', $order->id) }}"
                                            @click="openOptions = false"
                                            class="actions-dropdown-item edit-data">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            تعديل الحجز والتسعيرة
                                        </a>

                                        {{-- الموافقة على الحجز --}}
                                        @if ($order->status === 'pending')
                                            <button wire:click="approveOrder({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-brand-600 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-950/20">
                                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                الموافقة على الحجز
                                            </button>
                                        @endif

                                        {{-- إكمال الحجز --}}
                                        @if (in_array($order->status, ['approved', 'in_progress']))
                                            <button wire:click="completeOrder({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-success-600 dark:text-success-500 hover:bg-success-50 dark:hover:bg-success-950/20">
                                                <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                إكمال الحجز
                                            </button>
                                        @endif

                                        {{-- محادثة مع العميل --}}
                                        @if ($order->user_id)
                                            <button wire:click="startChatWithCustomer({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-brand-600 dark:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-950/20">
                                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                محادثة مع العميل
                                            </button>
                                        @endif

                                        {{-- إلغاء الحجز --}}
                                        @if ($order->status !== 'cancelled' && $order->status !== 'completed')
                                            <button wire:click="openCancelModal({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-error-600 dark:text-error-500 hover:bg-error-50 dark:hover:bg-error-950/20">
                                                <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                إلغاء الحجز
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500 dark:text-gray-400">
                                لا توجد حجوزات خدمات تطابق البحث أو الفلتر الحالي.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 3. الترقيم (Pagination) --}}
        @if ($this->orders->hasPages())
            <div class="px-4 py-3 mt-4 border-t border-gray-200 dark:border-gray-700">
                {{ $this->orders->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         النوافذ المنبثقة (Modals): إلغاء الحجز
       ══════════════════════════════════════════════════════════════════ --}}

    {{-- نافذة إلغاء الحجز --}}
    <div x-data="{ show: false }"
         x-on:open-modal.window="if ($event.detail == 'cancel-special-order-modal' || $event.detail?.[0] == 'cancel-special-order-modal') { show = true; }"
         x-on:close-modal.window="if ($event.detail == 'cancel-special-order-modal' || $event.detail?.[0] == 'cancel-special-order-modal') { show = false; }"
         x-show="show"
         class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
         style="display: none;">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="show = false"></div>

        <div @click.outside="show = false"
             class="relative z-10 p-6 w-full max-w-md bg-white rounded-3xl border border-gray-200 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800"
             dir="rtl">
            <div class="text-center">
                <div class="flex justify-center items-center mx-auto mb-4 w-14 h-14 rounded-full bg-error-50 text-error-500 dark:bg-error-500/15">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">
                    تأكيد إلغاء الحجز #{{ $cancellingOrderId }}
                </h4>
                <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                    يرجى توضيح سبب إلغاء هذا الحجز لحفظه في سجل العمليات.
                </p>
            </div>

            <form wire:submit.prevent="cancelOrder" class="space-y-4">
                <div>
                    <label class="block mb-1.5 text-xs font-medium text-right text-gray-700 dark:text-gray-300">
                        سبب الإلغاء <span class="text-error-500">*</span>
                    </label>
                    <textarea wire:model="cancellationReason" rows="3"
                        placeholder="اكتب سبب الإلغاء هنا..."
                        class="px-3 py-2 w-full text-sm text-right bg-transparent rounded-xl border border-gray-300 dark:border-gray-700 dark:text-white focus:border-error-500 focus:ring-1 focus:ring-error-500"></textarea>
                    @error('cancellationReason')
                        <span class="block mt-1 text-xs text-right text-error-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3 justify-center pt-2">
                    <button type="button" @click="show = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                        تراجع
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-xl transition bg-error-600 hover:bg-error-700">
                        <span wire:loading.remove wire:target="cancelOrder">تأكيد الإلغاء</span>
                        <span wire:loading wire:target="cancelOrder">جاري الإلغاء...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
