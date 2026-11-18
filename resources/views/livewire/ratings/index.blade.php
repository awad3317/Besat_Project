<div>
    {{-- 1. بطاقات الإحصائيات والفلاتر --}}
    <div class="flex flex-col flex-wrap gap-4 mb-4 sm:flex-row md:gap-6">
        {{-- إجمالي التقييمات --}}
        <div wire:click.debounce.150ms="applyFilter('all')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'all' && $starFilter === '') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('all')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي التقييمات</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['total'] ?? 0 }}
                </h4>
            </div>
        </div>

        {{-- التقييمات المرتفعة (4 - 5 نجوم) --}}
        <div wire:click.debounce.150ms="applyFilter('high')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'high') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('high')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">تقييمات مرتفعة (4 - 5)</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['high'] ?? 0 }}
                </h4>
            </div>
        </div>

        {{-- التقييمات المنخفضة (1 - 2 نجمة) --}}
        <div wire:click.debounce.150ms="applyFilter('low')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'low') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('low')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">تقييمات منخفضة (1 - 2)</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['low'] ?? 0 }}
                </h4>
            </div>
        </div>

        {{-- تقييمات بتعليق --}}
        <div wire:click.debounce.150ms="applyFilter('commented')" wire:loading.class="opacity-50" wire:target="applyFilter"
            class="relative flex cursor-pointer flex-col items-start justify-between rounded-xl bg-white p-4 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px] @if ($activeFilter == 'commented') border border-brand-500 dark:border-brand-500 @else border border-gray-200 dark:border-gray-800 @endif">
            <div wire:loading wire:target="applyFilter('commented')"
                class="flex absolute inset-0 z-10 justify-center items-center rounded-xl bg-white/70 dark:bg-gray-900/70">
                <div class="w-6 h-6 rounded-full border-2 animate-spin border-brand-500 border-t-transparent"></div>
            </div>
            <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
                <svg width="24" height="24" fill="none" stroke="#dc6803" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">تقييمات بتعليق</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ $this->stats['commented'] ?? 0 }}
                </h4>
            </div>
        </div>
    </div>

    {{-- 2. حاوية البحث وجدول تقييمات العملاء --}}
    <div class="space-y-5 sm:space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            {{-- شريط العنوان والبحث والفلترة --}}
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    {{-- <div class="inline-flex gap-2 items-center self-start px-3 py-1.5 bg-gray-100 rounded-lg dark:bg-gray-800">
                        <svg class="w-4 h-4" fill="#dc6803" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{-- <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                            المتوسط العام:
                            <strong class="font-bold text-gray-800 dark:text-white/90">
                                {{ number_format($this->stats['average'] ?? 0, 1) }}
                            </strong>
                            / 5
                        </span> --}}
                    {{-- </div>  --}}
                </div>

                <div class="flex flex-col gap-4 items-end sm:flex-row">
                    <div class="flex-1 w-full min-w-[250px]">
                        <label class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                            البحث
                        </label>
                        <div class="relative mt-1">
                            <input wire:model.live.debounce.300ms="search" type="text"
                                class="px-4 py-2.5 w-full h-10 text-sm text-right text-gray-800 bg-transparent rounded-lg border border-gray-300 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="ابحث برقم الرحلة، اسم العميل أو هاتفه، اسم السائق، أو التعليق..." />
                            <div class="flex absolute top-0 bottom-0 left-0 items-center px-3">
                                <div wire:loading wire:target="search">
                                    <div
                                        class="w-5 h-5 rounded-full border-2 animate-spin border-brand-500 border-t-transparent">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                  
                </div>
            </div>

            {{-- الجدول --}}
            <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                <div wire:loading.class="opacity-50" wire:target="search,applyFilter,starFilter,nextPage,previousPage,gotoPage"
                    class="overflow-visible rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <div class="overflow-x-auto max-w-full table-responsive-container">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-center sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">#</p>
                                    </th>
                                    <th class="px-5 py-3 text-right sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">العميل</p>
                                    </th>
                                    <th class="px-5 py-3 text-right sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">السائق</p>
                                    </th>
                                    <th class="px-5 py-3 text-right sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">التقييم</p>
                                    </th>
                                    <th class="px-5 py-3 text-right sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">التعليق</p>
                                    </th>
                                    <th class="px-5 py-3 text-center sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">رقم الرحلة</p>
                                    </th>
                                    <th class="px-5 py-3 text-center sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">تاريخ التقييم</p>
                                    </th>
                                    <th class="px-5 py-3 text-center sm:px-6">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">الإجراءات</p>
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @php
                                    $defaultDriverAvatar = asset('tailadmin/build/src/images/user/SO.jpg');
                                @endphp
                                @forelse ($this->ratings as $rating)
                                    <tr wire:key="rating-{{ $rating->id }}">
                                        {{-- # --}}
                                        <td class="px-5 py-4 text-center sm:px-6">
                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                #{{ $rating->id }}
                                            </p>
                                        </td>

                                        {{-- العميل --}}
                                        <td class="px-5 py-4 sm:px-6">
                                            @if ($rating->user)
                                                @php
                                                    $customerPhone = preg_replace('/_deleted_\d+$/', '', $rating->user->phone ?? '');
                                                @endphp
                                                <div>
                                                    <div class="flex gap-1.5 items-center">
                                                        <a href="{{ route('users.show', $rating->user->id) }}"
                                                            class="font-medium text-gray-800 transition-colors hover:text-brand-500 text-theme-sm dark:text-white/90 dark:hover:text-brand-400">
                                                            {{ $rating->user->name }}
                                                        </a>
                                                        @if ($rating->user->trashed())
                                                            <span
                                                                class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-medium text-red-600 dark:bg-red-500/15 dark:text-red-400">
                                                                محذوف
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($customerPhone)
                                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                                                            {{ $customerPhone }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="font-medium text-gray-500 text-theme-sm dark:text-gray-400">
                                                    مستخدم محذوف
                                                </span>
                                            @endif
                                        </td>

                                        {{-- السائق --}}
                                        <td class="px-5 py-4 sm:px-6">
                                            @if ($rating->driver)
                                                @php
                                                    $driverAvatar = $defaultDriverAvatar;
                                                    if (!empty($rating->driver->driver_image) && !str_contains($rating->driver->driver_image, 'default_driver1.png')) {
                                                        $driverAvatar = str_starts_with($rating->driver->driver_image, 'http')
                                                            ? $rating->driver->driver_image
                                                            : url($rating->driver->driver_image);
                                                    }
                                                @endphp
                                                <div class="flex gap-3 items-center">
                                                    <div
                                                        class="overflow-hidden flex-shrink-0 w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-800">
                                                        <img src="{{ $driverAvatar }}" alt="{{ $rating->driver->name }}"
                                                            class="object-cover w-full h-full"
                                                            onerror="this.onerror=null;this.src='{{ $defaultDriverAvatar }}';" />
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('drivers.show', $rating->driver->id) }}"
                                                            class="font-medium text-gray-800 transition-colors hover:text-brand-500 text-theme-sm dark:text-white/90 dark:hover:text-brand-400">
                                                            {{ $rating->driver->name }}
                                                        </a>
                                                        <div class="flex gap-1.5 items-center">
                                                            <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                                                {{ $rating->driver->phone }}
                                                            </span>
                                                            @if ($rating->driver->vehicle?->type)
                                                                <span class="text-gray-300 dark:text-gray-700">•</span>
                                                                <span class="text-[11px] font-medium text-warning-500 dark:text-warning/90">
                                                                    {{ $rating->driver->vehicle->type }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-500 text-theme-sm dark:text-gray-400">غير متاح</span>
                                            @endif
                                        </td>

                                        {{-- التقييم --}}
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex gap-1.5 items-center">
                                                <div class="flex items-center">
                                                    @for ($star = 1; $star <= 5; $star++)
                                                        <svg class="w-4 h-4"
                                                            fill="{{ $star <= (int) $rating->rating_value ? '#dc6803' : '#e5e7eb' }}"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                    ({{ (int) $rating->rating_value }}/5)
                                                </span>
                                            </div>
                                        </td>

                                        {{-- التعليق --}}
                                        <td class="px-5 py-4 max-w-xs sm:px-6">
                                            @if (!empty(trim((string) $rating->comment)))
                                                <p class="leading-relaxed text-gray-700 whitespace-normal break-words text-theme-sm dark:text-gray-300">
                                                    {{ $rating->comment }}
                                                </p>
                                            @else
                                                <span class="text-gray-400 text-theme-xs">لا يوجد تعليق</span>
                                            @endif
                                        </td>

                                        {{-- رقم الرحلة --}}
                                        <td class="px-5 py-4 text-center sm:px-6">
                                            @if ($rating->request_id)
                                                <a href="{{ route('request.show', $rating->request_id) }}"
                                                    class="inline-flex gap-1 items-center px-2.5 py-1 font-medium rounded-full transition bg-warning-50 text-theme-xs text-warning-600 hover:bg-warning-100 dark:bg-warning-500/15 dark:text-orange-400">
                                                    #{{ $rating->request_id }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-theme-xs">—</span>
                                            @endif
                                        </td>

                                        {{-- وقت التقييم --}}
                                        <td class="px-5 py-4 text-center sm:px-6">
                                            <span
                                                class="inline-block px-2 py-0.5 font-medium text-gray-600 bg-gray-100 rounded-full text-theme-xs dark:bg-gray-800 dark:text-gray-400"
                                                dir="ltr">
                                                {{ $rating->created_at ? $rating->created_at->format('Y/m/d h:i A') : '—' }}
                                            </span>
                                        </td>

                                        {{-- الإجراءات --}}
                                        <td class="px-5 py-4 text-center align-middle sm:px-6" x-data="{ openOptions: false }">
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
                                                    @if ($rating->request_id)
                                                        <a href="{{ route('request.show', $rating->request_id) }}"
                                                            class="actions-dropdown-item">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            تفاصيل الرحلة
                                                        </a>
                                                    @endif
                                                    @if ($rating->driver)
                                                        <a href="{{ route('drivers.show', $rating->driver->id) }}"
                                                            class="actions-dropdown-item">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                            </svg>
                                                            ملف السائق
                                                        </a>
                                                    @endif
                                                    @if ($rating->user)
                                                        <a href="{{ route('users.show', $rating->user->id) }}"
                                                            class="actions-dropdown-item">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                stroke-width="2" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            ملف العميل
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8">
                                            <div class="flex flex-col justify-center items-center text-center">
                                                <div
                                                    class="flex justify-center items-center mb-3 w-16 h-16 bg-gray-100 rounded-full dark:bg-gray-800">
                                                    <svg width="24" height="24" fill="none" stroke="#dc6803"
                                                        stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                    </svg>
                                                </div>
                                                <p class="font-medium text-gray-500 text-theme-sm dark:text-gray-400">
                                                    لا توجد تقييمات مطابقة حتى الآن
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($this->ratings->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                            {{ $this->ratings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
