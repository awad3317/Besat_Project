@extends('layouts.app')
@section('title', 'سجل النشاطات')
@section('Breadcrumb', 'سجل النشاطات')
@section('addButton')

@endsection
@section('style')

@endsection
@section('content')
    {{-- كروت الإحصائيات --}}
    <div class="flex flex-col flex-wrap gap-4 mb-6 sm:flex-row md:gap-6">
        {{-- إجمالي النشاطات --}}
        <div
            class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md hover:border-brand-500 dark:hover:border-brand-500 flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" stroke-width="2.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="mt-3 w-full text-right">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي النشاطات</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($stats['total'] ?? 0) }}</h4>
            </div>
        </div>

        {{-- نشاطات اليوم --}}
        <div
            class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md hover:border-brand-500 dark:hover:border-brand-500 flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                    stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="mt-3 w-full text-right">
                <span class="text-xs text-gray-500 dark:text-gray-400">نشاطات اليوم</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($stats['today'] ?? 0) }}</h4>
            </div>
        </div>

        {{-- نشاطات أمس --}}
        <div
            class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md hover:border-brand-500 dark:hover:border-brand-500 flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                    stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="mt-3 w-full text-right">
                <span class="text-xs text-gray-500 dark:text-gray-400">نشاطات أمس</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($stats['yesterday'] ?? 0) }}</h4>
            </div>
        </div>

        {{-- نشاطات الأسبوع الماضي --}}
        <div
            class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md hover:border-brand-500 dark:hover:border-brand-500 flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-warning-50 dark:bg-gray-800">
                <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                    stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
            <div class="mt-3 w-full text-right">
                <span class="text-xs text-gray-500 dark:text-gray-400">نشاطات آخر 7 أيام</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($stats['last_week'] ?? 0) }}</h4>
            </div>
        </div>
    </div>

    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <!-- table header start -->
                <thead>
                    <tr class="border-gray-100 border-y dark:border-gray-800">
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    #
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                    العملية
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    الوصف
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    المستخدم
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex justify-center items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    التاريخ
                                </p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- table header end -->

                <!-- table body start -->
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($logs as $log)
                        <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="py-3">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $loop->iteration }}
                                    </p>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="flex gap-3 items-center">
                                        <p
                                            class="px-6 py-4 text-sm font-bold whitespace-nowrap text-brand-600 dark:text-brand-400">
                                            {{ $log->action }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="max-w-xs truncate">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400"
                                            title="{{ $log->description ?? 'لا يوجد وصف' }}">
                                            {{ $log->description ?? 'لا يوجد وصف' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="flex gap-1 items-center">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $log->admin?->name ?? 'نظام' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex justify-center items-center">
                                    <div class="flex flex-col items-center">
                                        <p
                                            class="font-medium text-gray-500 text-theme-sm text-warning-500 dark:text-brand-400">
                                            {{ $log->created_at->format('h:i A') }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $log->created_at->format('Y-m-d') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-2">
                                <div class="flex flex-col justify-center items-center text-center">
                                    <div
                                        class="flex justify-center items-center mb-3 w-16 h-16 bg-gray-100 rounded-full dark:bg-gray-800">
                                        <svg :class="'menu-item-icon-active'"
                                            class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="font-medium text-gray-500 text-theme-sm dark:text-gray-400">
                                        لا توجد سجلات حتى الآن
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- table body end -->
            </table>

            @if ($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col justify-between items-center space-y-3 md:flex-row md:space-y-0">

                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            @if ($logs->onFirstPage())
                                <button disabled
                                    class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-not-allowed dark:bg-gray-800 dark:text-gray-600">
                                    السابق
                                </button>
                            @else
                                <a href="{{ $logs->previousPageUrl() }}"
                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white rounded-md transition-colors dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-400 hover:border-brand-400">
                                    السابق
                                </a>
                            @endif

                            @php
                                $current = $logs->currentPage();
                                $last = $logs->lastPage();
                                $start = max(1, $current - 2);
                                $end = min($last, $current + 2);

                                if ($end - $start < 4) {
                                    $start = max(1, $end - 4);
                                    $end = min($last, $start + 4);
                                }
                            @endphp

                            @if ($start > 1)
                                <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    ...
                                </span>
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $logs->currentPage())
                                    <span class="p-3 py-1.5 text-sm font-medium rounded-md bg-brand-500 dark:bg-brand-500">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $logs->url($page) }}"
                                        class="p-3 py-1.5 text-sm font-medium rounded-md text-brand-400 dark:text-brand-400 bg-brand-400 dark:bg-gray-800">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor

                            @if ($end < $last)
                                <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    ...
                                </span>
                            @endif

                            @if ($logs->hasMorePages())
                                <a href="{{ $logs->nextPageUrl() }}"
                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white rounded-md transition-colors dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-brand-400 hover:border-brand-400">
                                    التالي
                                </a>
                            @else
                                <button disabled
                                    class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 rounded-md cursor-not-allowed dark:bg-gray-800 dark:text-gray-600">
                                    التالي
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection

@section('script')

@endsection
