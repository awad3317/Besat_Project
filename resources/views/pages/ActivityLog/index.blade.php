@extends('layouts.app')
@section('title', 'سجل النشاطات')
@section('Breadcrumb', 'سجل النشاطات')
@section('addButton')

@endsection
@section('style')

@endsection
@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
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
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
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
                            <div class="flex items-center justify-center">
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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                            <td class="py-3">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $loop->iteration }}
                                    </p>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="flex items-center gap-3">
                                        <p
                                            class="px-6 py-4 whitespace-nowrap text-sm font-bold text-brand-600 dark:text-brand-400">
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
                                    <div class="flex items-center gap-1">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $log->admin?->name ?? 'نظام' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center justify-center">
                                    <div class="flex flex-col items-center">
                                        <p class="text-gray-500 text-theme-sm text-warning-500 dark:text-brand-400 font-medium">
                                            {{ $log->created_at->format('h:i A') }}
                                        </p>
                                        <p class="text-gray-400 text-xs dark:text-gray-500">
                                            {{ $log->created_at->format('Y-m-d') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-2">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div
                                        class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                        <svg :class=" 'menu-item-icon-active' " class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                                        لا توجد سجلات حتى الآن
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- table body end -->
            </table>

            @if($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">

                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            @if($logs->onFirstPage())
                                <button disabled
                                    class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                                    السابق
                                </button>
                            @else
                                <a href="{{ $logs->previousPageUrl() }}"
                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors hover:text-brand-400 hover:border-brand-400">
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

                            @if($start > 1)
                                <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    ...
                                </span>
                            @endif

                            @for($page = $start; $page <= $end; $page++)
                                @if($page == $logs->currentPage())
                                    <span class="p-3 py-1.5 text-sm font-medium bg-brand-500 dark:bg-brand-500 rounded-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $logs->url($page) }}"
                                        class="p-3 py-1.5 text-sm font-medium text-brand-400 dark:text-brand-400 bg-brand-400 dark:bg-gray-800 rounded-md">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor

                            @if($end < $last)
                                <span class="px-3 py-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    ...
                                </span>
                            @endif

                            @if($logs->hasMorePages())
                                <a href="{{ $logs->nextPageUrl() }}"
                                    class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors hover:text-brand-400 hover:border-brand-400">
                                    التالي
                                </a>
                            @else
                                <button disabled
                                    class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
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