<div>
    {{-- ════════════ 1. بطاقات الإحصائيات والفلترة التفاعلية (4 Interactive Stat Cards) ════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 special-orders-stats-grid">
        {{-- 1. إجمالي الطلبات --}}
        <div wire:click="applyFilter('all')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex flex-col items-start justify-between rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md {{ $activeFilter === 'all' ? 'border-2 border-orange-500 border-brand-500 bg-orange-50/10 dark:bg-gray-800' : 'border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700' }}">
            <div wire:loading wire:target="applyFilter('all')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-2xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 dark:bg-gray-700 flex items-center justify-center">
                <svg fill="#F59E0B" width="22" height="22" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                </svg>
            </div>
            <div class="mt-4 w-full text-right">
                <span class="block text-sm text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $this->stats['total'] ?? 0 }}
                </h4>
            </div>
        </div>

        {{-- 2. الطلبات الجارية --}}
        <div wire:click="applyFilter('in_progress')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex flex-col items-start justify-between rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md {{ $activeFilter === 'in_progress' ? 'border-2 border-orange-500 border-brand-500 bg-orange-50/10 dark:bg-gray-800' : 'border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700' }}">
            <div wire:loading wire:target="applyFilter('in_progress')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-2xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 dark:bg-gray-700 flex items-center justify-center">
                <svg fill="#F59E0B" width="22" height="22" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.7,2c-0.1,0-0.1,0-0.2,0c0,0,0,0-0.1,0v0c-0.2,0-0.3,0-0.5,0l0.2,2c0.4,0,0.9,0,1.3,0c4,0.3,7.3,3.5,7.5,7.6 c0.2,4.4-3.2,8.2-7.6,8.4c0,0-0.1,0-0.2,0c-0.3,0-0.7,0-1,0L11,22c0.4,0,0.8,0,1.3,0c0.1,0,0.3,0,0.4,0v0c5.4-0.4,9.5-5,9.3-10.4 c-0.2-5.1-4.3-9.1-9.3-9.5v0c0,0,0,0,0,0c-0.2,0-0.3,0-0.5,0C12,2,11.9,2,11.7,2z M8.2,2.7C7.7,3,7.2,3.2,6.7,3.5l1.1,1.7 C8.1,5,8.5,4.8,8.9,4.6L8.2,2.7z M4.5,5.4c-0.4,0.4-0.7,0.9-1,1.3l1.7,1C5.4,7.4,5.7,7.1,6,6.7L4.5,5.4z M15.4,8.4l-4.6,5.2 l-2.7-2.1L7,13.2l4.2,3.2l5.8-6.6L15.4,8.4z M2.4,9c-0.2,0.5-0.3,1.1-0.3,1.6l2,0.3c0.1-0.4,0.1-0.9,0.3-1.3L2.4,9z M4.1,13l-2,0.2 c0,0.1,0,0.2,0,0.3c0.1,0.4,0.2,0.9,0.3,1.3l1.9-0.6c-0.1-0.3-0.2-0.7-0.2-1.1L4.1,13z M5.2,16.2l-1.7,1.1c0.3,0.5,0.6,0.9,1,1.3 L6,17.3C5.7,16.9,5.4,16.6,5.2,16.2z M7.8,18.8l-1.1,1.7c0.5,0.3,1,0.5,1.5,0.8l0.8-1.8C8.5,19.2,8.1,19,7.8,18.8z"></path>
                </svg>
            </div>
            <div class="mt-4 w-full text-right">
                <span class="block text-sm text-gray-500 dark:text-gray-400">الطلبات الجارية</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $this->stats['in_progress'] ?? 0 }}
                </h4>
            </div>
        </div>

        {{-- 3. الطلبات المكتملة --}}
        <div wire:click="applyFilter('completed')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex flex-col items-start justify-between rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md {{ $activeFilter === 'completed' ? 'border-2 border-orange-500 border-brand-500 bg-orange-50/10 dark:bg-gray-800' : 'border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700' }}">
            <div wire:loading wire:target="applyFilter('completed')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-2xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 dark:bg-gray-700 flex items-center justify-center">
                <svg width="22" height="22" viewBox="0 0 1024 1024" fill="#F59E0B" xmlns="http://www.w3.org/2000/svg">
                    <path d="M824.8 1003.2H203.2c-12.8 0-25.6-2.4-37.6-7.2-11.2-4.8-21.6-12-30.4-20.8-8.8-8.8-16-19.2-20.8-30.4-4.8-12-7.2-24-7.2-37.6V260c0-12.8 2.4-25.6 7.2-37.6 4.8-11.2 12-21.6 20.8-30.4 8.8-8.8 19.2-16 30.4-20.8 12-4.8 24-7.2 37.6-7.2h94.4v48H203.2c-26.4 0-48 21.6-48 48v647.2c0 26.4 21.6 48 48 48h621.6c26.4 0 48-21.6 48-48V260c0-26.4-21.6-48-48-48H730.4v-48H824c12.8 0 25.6 2.4 37.6 7.2 11.2 4.8 21.6 12 30.4 20.8 8.8 8.8 16 19.2 20.8 30.4 4.8 12 7.2 24 7.2 37.6v647.2c0 12.8-2.4 25.6-7.2 37.6-4.8 11.2-12 21.6-20.8 30.4-8.8 8.8-19.2 16-30.4 20.8-11.2 4.8-24 7.2-36.8 7.2z"></path>
                    <path d="M752.8 308H274.4V152.8c0-32.8 26.4-60 60-60h61.6c22.4-44 67.2-72.8 117.6-72.8 50.4 0 95.2 28.8 117.6 72.8h61.6c32.8 0 60 26.4 60 60v155.2m-430.4-48h382.4V152.8c0-6.4-5.6-12-12-12H598.4l-5.6-16c-12-33.6-43.2-56-79.2-56s-67.2 22.4-79.2 56l-5.6 16H334.4c-6.4 0-12 5.6-12 12v107.2zM432.8 792c-6.4 0-12-2.4-16.8-7.2L252.8 621.6c-4.8-4.8-7.2-10.4-7.2-16.8s2.4-12 7.2-16.8c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2L418.4 720c4 4 8.8 5.6 13.6 5.6s10.4-1.6 13.6-5.6l295.2-295.2c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2c9.6 9.6 9.6 24 0 33.6L449.6 784.8c-4.8 4-11.2 7.2-16.8 7.2z"></path>
                </svg>
            </div>
            <div class="mt-4 w-full text-right">
                <span class="block text-sm text-gray-500 dark:text-gray-400">الطلبات المكتملة</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $this->stats['completed'] ?? 0 }}
                </h4>
            </div>
        </div>

        {{-- 4. الطلبات الملغية --}}
        <div wire:click="applyFilter('cancelled')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex flex-col items-start justify-between rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md {{ $activeFilter === 'cancelled' ? 'border-2 border-orange-500 border-brand-500 bg-orange-50/10 dark:bg-gray-800' : 'border border-gray-100 bg-white dark:bg-gray-800 dark:border-gray-700' }}">
            <div wire:loading wire:target="applyFilter('cancelled')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-2xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 dark:bg-gray-700 flex items-center justify-center">
                <svg width="22" height="22" viewBox="0 0 512 512" fill="#F59E0B" xmlns="http://www.w3.org/2000/svg">
                    <path d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z"></path>
                </svg>
            </div>
            <div class="mt-4 w-full text-right">
                <span class="block text-sm text-gray-500 dark:text-gray-400">الطلبات الملغية</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $this->stats['cancelled'] ?? 0 }}
                </h4>
            </div>
        </div>
    </div>

    {{-- ════════════ 2. الحاوية الرئيسية للبحث والجدول (Main Content Card) ════════════ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
        {{-- قسم البحث --}}
        <div class="mb-5 text-right">
            <label class="block mb-1.5 font-medium text-gray-500 text-theme-xs dark:text-gray-400 text-right">البحث</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="w-full rounded-xl border border-gray-200 focus:border-orange-500 focus:ring-orange-500 text-right shadow-theme-xs h-10 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="ابحث باسم العميل، الهاتف، السائق..." />
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
                    <tr class="border-y border-gray-100 dark:border-gray-800">
                        <th class="py-3 text-right px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم الطلب</p>
                        </th>
                        <th class="py-3 text-right px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">السائق</p>
                        </th>
                        <th class="py-3 text-right px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">العميل</p>
                        </th>
                        <th class="py-3 text-center px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">السعر</p>
                        </th>
                        <th class="py-3 text-center px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">حالة الطلب</p>
                        </th>
                        <th class="py-3 text-center px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">من - الى</p>
                        </th>
                        <th class="py-3 text-center px-3">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">الإجراءات</p>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($this->orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01] transition">
                            {{-- 1. رقم الطلب --}}
                            <td class="py-3 px-3">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800 text-theme-sm dark:text-white/90">#{{ $order->id }}</span>
                                    @if ($order->title)
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[140px]" title="{{ $order->title }}">
                                            {{ $order->title }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 2. السائق --}}
                            <td class="py-3 px-3">
                                @if ($order->driver)
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 overflow-hidden rounded-full border border-gray-200 dark:border-gray-700 shrink-0">
                                            <img src="{{ $order->driver->driver_image && !str_contains($order->driver->driver_image, 'default_driver1.png') ? url($order->driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                                alt="{{ $order->driver->name }}"
                                                class="w-full h-full object-cover"
                                                onerror="this.onerror=null;this.src='{{ asset('tailadmin/build/src/images/user/SO.jpg') }}';" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                {{ $order->driver->name }}
                                            </p>
                                            <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                                {{ $order->driver->phone }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    @if ($order->status !== 'cancelled' && $order->status !== 'completed')
                                        <button wire:click="openAssignDriverModal({{ $order->id }})"
                                            wire:loading.attr="disabled"
                                            class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-medium text-warning-600 bg-warning-50 rounded-full transition hover:bg-warning-100 dark:bg-warning-500/15 dark:text-warning-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="8.5" cy="7" r="4"></circle>
                                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                                <line x1="23" y1="11" x2="17" y2="11"></line>
                                            </svg>
                                            <span>لم يتم التعيين (تعيين سائق)</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-400">
                                            لم يتم التعيين
                                        </span>
                                    @endif
                                @endif
                            </td>

                            {{-- 3. العميل --}}
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $order->customer_name ?: ($order->user?->name ?? 'عميل غير محدد') }}
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400" dir="ltr">
                                            {{ $order->customer_phone ?: ($order->user?->phone ?? '—') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- 4. السعر --}}
                            <td class="py-3 px-3 text-center">
                                @if ($order->price !== null && $order->price > 0)
                                    <span class="font-semibold text-gray-800 text-theme-sm dark:text-white/90">
                                        {{ number_format($order->price) }}
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ config('app.currency_symbol', 'ر.ي') }}</span>
                                    </span>
                                @else
                                    <button wire:click="openEditModal({{ $order->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600 dark:bg-gray-700 dark:text-gray-300 transition">
                                        <span>تحديد السعر</span>
                                    </button>
                                @endif
                            </td>

                            {{-- 5. حالة الطلب --}}
                            <td class="py-3 px-3 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-theme-xs font-medium {{ $order->status_class }}">
                                    {{ $order->status_text }}
                                </span>
                            </td>

                            {{-- 6. من - الى --}}
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    <span class="rounded-full bg-warning-50 px-2.5 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                        {{ $order->start_address ?: 'غير محدد' }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    <span class="rounded-full px-2.5 py-0.5 text-theme-xs font-medium bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                        {{ $order->end_address ?: 'غير محدد' }}
                                    </span>
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

                                        {{-- تعديل الطلب / التسعيرة --}}
                                        <button wire:click="openEditModal({{ $order->id }})"
                                            @click="openOptions = false"
                                            class="actions-dropdown-item text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            تعديل الطلب والتسعيرة
                                        </button>

                                        {{-- تعيين أو تغيير السائق --}}
                                        @if ($order->status !== 'cancelled' && $order->status !== 'completed')
                                            <button wire:click="openAssignDriverModal({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-warning-600 dark:text-warning-500 hover:bg-warning-50 dark:hover:bg-warning-950/20">
                                                <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $order->driver_id ? 'تغيير السائق' : 'تعيين سائق' }}
                                            </button>
                                        @endif

                                        {{-- إكمال الطلب --}}
                                        @if ($order->status === 'in_progress')
                                            <button wire:click="completeOrder({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-success-600 dark:text-success-500 hover:bg-success-50 dark:hover:bg-success-950/20">
                                                <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                إكمال الطلب
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

                                        {{-- محادثة مع السائق --}}
                                        @if ($order->driver_id)
                                            <button wire:click="startChatWithDriver({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-blue-light-600 dark:text-blue-light-500 hover:bg-blue-light-50 dark:hover:bg-blue-light-950/20">
                                                <svg class="w-4 h-4 text-blue-light-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                محادثة السائق
                                            </button>
                                        @endif

                                        {{-- إلغاء الطلب --}}
                                        @if ($order->status !== 'cancelled' && $order->status !== 'completed')
                                            <button wire:click="openCancelModal({{ $order->id }})"
                                                @click="openOptions = false"
                                                class="actions-dropdown-item text-error-600 dark:text-error-500 hover:bg-error-50 dark:hover:bg-error-950/20">
                                                <svg class="w-4 h-4 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                إلغاء الطلب
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500 dark:text-gray-400">
                                لا توجد طلبات تطابق البحث أو الفلتر الحالي.
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
         النوافذ المنبثقة (Modals): تعيين سائق، تعديل الطلب، وإلغاء الطلب
       ══════════════════════════════════════════════════════════════════ --}}

    {{-- 1. نافذة تعيين سائق للطلب الخاص --}}
    @php
        $selOrder = $this->selectedOrder;
    @endphp
    <div x-data="{ show: false, isSuccess: false }"
         x-on:open-modal.window="if ($event.detail === 'assign-special-driver-modal') { show = true; isSuccess = false; }"
         x-on:close-modal.window="if ($event.detail === 'assign-special-driver-modal') { show = false; }"
         x-on:special-driver-assigned.window="isSuccess = true; setTimeout(() => { show = false; isSuccess = false; $wire.clearSelectedOrder(); }, 1400)"
         x-show="show"
         class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
         style="display: none;">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="show = false"></div>

        <div @click.outside="show = false"
             class="relative z-10 w-full max-w-2xl bg-white rounded-3xl border border-gray-200 p-6 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800"
             dir="rtl">

            {{-- شاشة النجاح --}}
            <div x-show="isSuccess"
                 class="flex absolute inset-0 z-50 flex-col justify-center items-center bg-white rounded-3xl dark:bg-gray-900">
                <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-success-50 text-success-500 dark:bg-success-500/15">
                    <svg class="w-10 h-10 text-success-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h4 class="mb-1 text-xl font-bold text-gray-800 dark:text-white">تم تعيين السائق بنجاح!</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">تم إسناد الطلب الخاص وتحديث الحالة إلى قيد التنفيذ.</p>
            </div>

            {{-- رأس النافذة --}}
            <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">
                        تعيين سائق للطلب الخاص #{{ $selectedOrderId }}
                    </h4>
                    @if ($selOrder)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-warning-600">من: {{ $selOrder->start_address ?: 'غير محدد' }}</span>
                            <span class="mx-1">←</span>
                            <span class="font-semibold text-blue-light-500">إلى: {{ $selOrder->end_address ?: 'غير محدد' }}</span>
                        </p>
                    @endif
                </div>
                <button @click="show = false"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- تسعيرة الطلب الاختيارية عند التعيين --}}
            <div class="mb-4 p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <label class="block mb-1.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                    سعر الطلب الخاص ({{ config('app.currency_symbol', 'ر.ي') }})
                </label>
                <input type="number" wire:model="assignOrderPrice" placeholder="أدخل أو عدل سعر الرحلة قبل التعيين..."
                    class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
            </div>

            {{-- البحث والفلترة للسائقين --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-4">
                <div class="relative flex-1">
                    <input wire:model.live.debounce.300ms="driverSearch" type="text"
                        placeholder="ابحث باسم السائق، الهاتف، أو رقم اللوحة..."
                        class="px-4 py-2.5 w-full h-10 text-sm text-right text-gray-800 bg-transparent rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                </div>
                <label class="flex gap-2 items-center px-3 py-2 text-xs font-medium text-gray-700 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <input type="checkbox" wire:model.live="onlineDriversOnly"
                        class="rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
                    <span class="w-2 h-2 rounded-full bg-success-500"></span>
                    <span>المتصلون فقط</span>
                </label>
            </div>

            {{-- قائمة السائقين --}}
            <div class="overflow-y-auto max-h-80 space-y-2 pr-1">
                @forelse ($this->drivers as $driver)
                    <div class="flex justify-between items-center p-3 rounded-xl border border-gray-200 bg-white hover:border-brand-500 transition dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex gap-3 items-center">
                            <div class="relative shrink-0">
                                <img src="{{ $driver->driver_image && !str_contains($driver->driver_image, 'default_driver1.png') ? url($driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                     alt="{{ $driver->name }}"
                                     class="w-11 h-11 rounded-full object-cover border-2 {{ $driver->is_online ? 'border-success-500' : 'border-gray-200 dark:border-gray-700' }}"
                                     onerror="this.onerror=null;this.src='{{ asset('tailadmin/build/src/images/user/SO.jpg') }}';" />
                                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 {{ $driver->is_online ? 'bg-success-500' : 'bg-gray-400' }}"></span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $driver->name }}</p>
                                <div class="flex gap-2 items-center text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $driver->phone }}</span>
                                    @if ($driver->vehicle)
                                        <span>•</span>
                                        <span class="text-brand-600 dark:text-brand-400 font-medium">{{ $driver->vehicle->type }}</span>
                                    @endif
                                    @if ($driver->plate_number)
                                        <span>•</span>
                                        <span>لوحة: {{ $driver->plate_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button wire:click="assignDriver({{ $driver->id }})"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-xs font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600 transition shrink-0">
                            <span wire:loading.remove wire:target="assignDriver({{ $driver->id }})">تعيين</span>
                            <span wire:loading wire:target="assignDriver({{ $driver->id }})">جاري...</span>
                        </button>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                        لا يوجد سائقون متاحون مطابقون للبحث.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 2. نافذة تعديل الطلب والتسعيرة --}}
    <div x-data="{ show: false }"
         x-on:open-modal.window="if ($event.detail === 'edit-special-order-modal') { show = true; }"
         x-on:close-modal.window="if ($event.detail === 'edit-special-order-modal') { show = false; }"
         x-show="show"
         class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
         style="display: none;">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="show = false"></div>

        <div @click.outside="show = false"
             class="relative z-10 w-full max-w-xl bg-white rounded-3xl border border-gray-200 p-6 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800"
             dir="rtl">
            <div class="flex justify-between items-center pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">
                    تعديل الطلب الخاص #{{ $editingOrderId }}
                </h4>
                <button @click="show = false"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="updateOrder" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">عنوان الطلب *</label>
                        <input type="text" wire:model="editTitle"
                            class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500" />
                        @error('editTitle') <span class="text-xs text-error-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">السعر ({{ config('app.currency_symbol', 'ر.ي') }})</label>
                        <input type="number" wire:model="editPrice" placeholder="أدخل السعر"
                            class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500" />
                        @error('editPrice') <span class="text-xs text-error-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">اسم العميل</label>
                        <input type="text" wire:model="editCustomerName"
                            class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500" />
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">رقم هاتف العميل</label>
                        <input type="text" wire:model="editCustomerPhone" dir="ltr"
                            class="w-full h-10 px-3 py-2 text-sm text-right rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500" />
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">نقطة الانطلاق *</label>
                        <input type="text" wire:model="editStartAddress"
                            class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500" />
                        @error('editStartAddress') <span class="text-xs text-error-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">نقطة الوصول *</label>
                        <input type="text" wire:model="editEndAddress"
                            class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500" />
                        @error('editEndAddress') <span class="text-xs text-error-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">حالة الطلب</label>
                        <select wire:model="editStatus"
                            class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:border-brand-500">
                            <option value="pending">قيد الانتظار</option>
                            <option value="searching_driver">جاري البحث عن سائق</option>
                            <option value="paused">بانتظار التوجيه</option>
                            <option value="in_progress">قيد التنفيذ</option>
                            <option value="completed">مكتملة</option>
                            <option value="cancelled">ملغية</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">الوصف / الملاحظات</label>
                        <textarea wire:model="editDescription" rows="3"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-brand-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="show = false"
                        class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                        إلغاء
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
                        <span wire:loading.remove wire:target="updateOrder">حفظ التعديلات</span>
                        <span wire:loading wire:target="updateOrder">جاري الحفظ...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. نافذة إلغاء الطلب --}}
    <div x-data="{ show: false }"
         x-on:open-modal.window="if ($event.detail === 'cancel-special-order-modal') { show = true; }"
         x-on:close-modal.window="if ($event.detail === 'cancel-special-order-modal') { show = false; }"
         x-show="show"
         class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
         style="display: none;">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="show = false"></div>

        <div @click.outside="show = false"
             class="relative z-10 w-full max-w-md bg-white rounded-3xl border border-gray-200 p-6 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800"
             dir="rtl">
            <div class="text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-error-50 text-error-500 dark:bg-error-500/15">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">
                    تأكيد إلغاء الطلب الخاص #{{ $cancellingOrderId }}
                </h4>
                <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                    يرجى توضيح سبب إلغاء هذا الطلب لحفظه في سجل العمليات.
                </p>
            </div>

            <form wire:submit.prevent="cancelOrder" class="space-y-4">
                <div>
                    <label class="block mb-1.5 text-xs font-medium text-right text-gray-700 dark:text-gray-300">
                        سبب الإلغاء <span class="text-error-500">*</span>
                    </label>
                    <textarea wire:model="cancellationReason" rows="3"
                        placeholder="اكتب سبب الإلغاء هنا..."
                        class="w-full px-3 py-2 text-sm text-right rounded-xl border border-gray-300 bg-transparent dark:border-gray-700 dark:text-white focus:border-error-500 focus:ring-1 focus:ring-error-500"></textarea>
                    @error('cancellationReason')
                        <span class="block mt-1 text-xs text-right text-error-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-center gap-3 pt-2">
                    <button type="button" @click="show = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                        تراجع
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-error-600 rounded-xl hover:bg-error-700 transition">
                        <span wire:loading.remove wire:target="cancelOrder">تأكيد الإلغاء</span>
                        <span wire:loading wire:target="cancelOrder">جاري الإلغاء...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .special-orders-stats-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .special-orders-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .special-orders-stats-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .border-orange-500 {
            border-color: #F59E0B !important;
        }
        .bg-orange-50 {
            background-color: #FFFBEB;
        }
        .bg-orange-50\/10 {
            background-color: rgba(245, 158, 11, 0.06);
        }
        .text-orange-500 {
            color: #F59E0B;
        }
    </style>
</div>
