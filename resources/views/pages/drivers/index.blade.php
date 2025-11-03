@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'إدارة السائقين')
@section('content')
    <div class="space-y-5 sm:space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">


            <div class="px-5 py-4 sm:px-6 sm:py-5 flex items-center justify-between flex-row-reverse">
                <div class="relative" x-data="{ showFilter: false }">
                    <button
                        class="shadow-theme-xs flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                        @click="showFilter = !showFilter" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none">
                            <path
                                d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Filter
                    </button>

                    <div x-show="showFilter" @click.away="showFilter = false"
                        class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-5">
                            <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Category
                            </label>
                            <input type="text"
                                class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="Search category..." />
                        </div>
                        <div class="mb-5">
                            <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Company
                            </label>
                            <input type="text"
                                class="shadow-theme-xs h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="Search company..." />
                        </div>
                        <button
                            class="bg-brand-500 hover:bg-brand-600 h-10 w-full rounded-lg px-3 py-2 text-sm font-medium text-white">
                            Apply
                        </button>
                    </div>
                </div>
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    السائقين
                </h3>

            </div>
        </div>
    </div>

    <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
        <!-- ====== Table Six Start -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
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
                                        نوعه
                                    </p>
                                </div>
                            </th>
                            <th class="px-5 py-3 sm:px-6">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        حالته
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
                         
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td>
                                <div
                                    class="col-span-3 flex items-center border-r border-gray-100 px-4 py-3 dark:border-gray-800">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 overflow-hidden rounded-full">
                                            <img src="{{ asset('tailadmin/src/images/user/user-01.jpg') }}"
                                                alt="user" />
                                        </div>
                                        <div>
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                    احمد شرجبي
                                                </span>
                                                <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                                                    +967780236552
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">

                                <div class="flex items-center">
                                    <div class="flex items-center gap-3">
                                        <div>

                                            <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                                                +967780236552
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        +967780236551
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center">
                                    <p
                                        class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">
                                        مستخدم عادي
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="relative flex items-center gap-2">
                                    <span 
                                        class="relative h-2 w-2 rounded-full bg-orange-400 dark:bg-orange-500">
                                        <span
                                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 dark:bg-orange-500 opacity-75"></span>
                                    </span>
                                    <p
                                        class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">
                                        نشط
                                    </p>
                                </div>
                            </td>

         
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
        <!-- ====== Table Six End -->
    </div>
    </div>
    </div>
@endsection
