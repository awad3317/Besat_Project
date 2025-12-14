@extends('layouts.app')
@section('title', 'إدارة المستخدمين')
@section('Breadcrumb', 'إدارة المستخدمين')
@section('content')
    <div class="space-y-5 sm:space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">

                <div class="flex flex-col sm:flex-row gap-4 items-end">

                    <!-- حقل البحث -->
                    <div class="flex-1 min-w-[250px]">
                        <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300 text-right">
                            البحث
                        </label>
                        <input type="text"
                            class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 text-right"
                            placeholder="أدخل نص البحث..." />
                    </div>

                    <!-- خيار المتصلين فقط / الكل -->
                    <div class="min-w-[180px]">
                        <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300 text-right">
                            الحالة
                        </label>
                        <select
                            class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 text-right">
                            <option value="all">الكل</option>
                            <option value="connected">المتصلين فقط</option>
                        </select>
                    </div>

                    <!-- زر التطبيق -->
                    <button
                        class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px]">
                        تطبيق
                    </button>

                </div>

            </div>

            <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                <!-- ====== Table Six Start -->
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full">
                            <!-- table header start -->
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الأسم
                                            </p>
                                        </div>
                                    </th>
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                رقم الواتساب
                                            </p>
                                        </div>
                                    </th>
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الحظر
                                            </p>
                                        </div>
                                    </th>
                                    <th class="px-5 py-3 sm:px-6">
                                        <div class="flex items-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الطلبات
                                            </p>
                                        </div>
                                    </th>
                                    <th class="py-3">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                الإجراءات
                                            </p>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- table header end -->
                            <!-- table body start -->
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <div class="flex items-center gap-3">
                                                    <div>
                                                        <span
                                                            class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                            {{ $user->name }}
                                                        </span>
                                                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                                                            {{ $user->phone }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $user->whatsapp_number }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div x-data="{ switcherToggle: {{ $user->is_banned ? 'true' : 'false' }} }">
                                                <label for="'toggle_{{ $user->id }}'"
                                                    class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                                                    <div class="relative">
                                                        <input type="checkbox" :id="'toggle_{{ $user->id }}'" class="sr-only"
                                                            @change="toggleBan({{ $user->id }}, switcherToggle)" />
                                                        <div class="block h-6 w-11 rounded-full"
                                                            :class="switcherToggle ?  'bg-error-500' : 'bg-success-500 '"></div>
                                                        <div :class="switcherToggle ? 'translate-x-0' : 'translate-x-full'"
                                                            class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear">
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $user->requests()->count() }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center justify-center">
                                                <button onclick="window.location.href='{{ route('users.show', 1) }}'"
                                                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    تفاصيل
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="p-2">
                                            <div class="flex flex-col items-center justify-center text-center">
                                                <div
                                                    class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                                                    لا يوجد مستخدمين حتى الآن
                                                </p>
                                            </div>
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>
                        </table>
                        @if($users->hasPages())
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">

                                    <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                        @if($users->onFirstPage())
                                            <button disabled
                                                class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                                                السابق
                                            </button>
                                        @else
                                            <a href="{{ $users->previousPageUrl() }}"
                                                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors hover:text-brand-400 hover:border-brand-400">
                                                السابق
                                            </a>
                                        @endif

                                        @php
                                            $current = $users->currentPage();
                                            $last = $users->lastPage();
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
                                            @if($page == $users->currentPage())
                                                <span class="p-3 py-1.5 text-sm font-medium bg-brand-500 dark:bg-brand-500 rounded-md">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ $users->url($page) }}"
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

                                        @if($users->hasMorePages())
                                            <a href="{{ $users->nextPageUrl() }}"
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
                <!-- ====== Table Six End -->
            </div>
        </div>
    </div>

@endsection