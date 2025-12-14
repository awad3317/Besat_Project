@extends('layouts.app')
@section('title', 'إدارة السائقين')
@section('Breadcrumb', 'إدارة السائقين')
@section('content')

    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        <div
            class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" height="30" width="30" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 438.775 438.775" xml:space="preserve">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path
                            d="M135.646,150.005c10.719,6.367,22.735,9.551,34.752,9.551c12.018,0,24.036-3.185,34.754-9.552 c25.497-15.148,39.324-45.114,37.937-82.214c-1.679-44.887-38.677-66.603-72.341-67.784c-0.234-0.008-0.467-0.008-0.701,0 C136.382,1.188,99.384,22.903,97.705,67.79C96.318,104.891,110.148,134.856,135.646,150.005z M170.397,19.79 c25.114,1.012,50.86,16.348,52.649,47.348h-7.381c-11.385-13-27.598-20-45.233-20c-0.001,0-0.002,0-0.003,0 c-17.672,0-33.909,7-45.3,20h-7.382C119.536,36.138,145.282,20.802,170.397,19.79z M129.925,87.138c3.188,0,6.184-1.411,8.067-3.982 c7.617-10.399,19.441-16.31,32.438-16.309c12.956,0.001,24.755,5.884,32.37,16.281c1.883,2.572,4.88,4.01,8.067,4.01h11.303 c-2.984,21-12.474,36.794-27.235,45.563c-15.136,8.992-33.939,8.938-49.075-0.054c-14.762-8.77-24.252-24.509-27.237-45.509H129.925 z M380.67,326.878c0,61.7-50.196,111.897-111.896,111.897c-5.522,0-10-4.478-10-10s4.478-10,10-10 c50.672,0,91.896-41.225,91.896-91.897c0-5.522,4.478-10,10-10S380.67,321.355,380.67,326.878z M339.234,324.878 c0-4.585-0.429-9.073-1.246-13.424c-0.028-0.172-0.062-0.343-0.099-0.515c-6.515-33.312-35.925-58.523-71.114-58.523 c-39.958,0-72.467,32.506-72.467,72.462c0,39.954,32.509,72.46,72.467,72.46C306.729,397.338,339.234,364.832,339.234,324.878z M318.802,327.059c-1.009,24.534-18.414,44.776-42.414,49.323v-41.769C291.388,333.821,305.631,331.262,318.802,327.059z M266.775,272.416c22.655,0,42.002,14.438,49.326,34.595c-14.874,5.163-31.744,7.867-49.326,7.867 c-17.581,0-34.455-2.705-49.333-7.869C224.767,286.853,244.116,272.416,266.775,272.416z M256.388,376.382 c-23-4.547-41.416-24.789-42.422-49.326c13.173,4.204,27.422,6.766,42.422,7.558V376.382z M101.505,248.654L80.128,364.138h84.43 c5.522,0,10,4.477,10,10c0,5.522-4.477,10-10,10H68.106c-2.971,0-5.788-1.393-7.688-3.677c-1.899-2.284-2.686-5.365-2.145-8.287 L81.839,244.94c5.605-30.255,27.82-52.549,60.95-61.167c17.559-4.568,36.928-4.628,54.538-0.174 c19.041,4.817,34.973,14.441,46.073,27.832c3.524,4.252,2.935,10.556-1.317,14.08c-4.253,3.527-10.556,2.935-14.08-1.316 c-16.834-20.305-49.804-28.968-80.179-21.066c-9.588,2.494-17.967,6.422-24.9,11.552l51.579,51.586 c3.905,3.905,3.905,10.236-0.001,14.142c-3.904,3.906-10.237,3.905-14.142-0.001l-51.198-51.204 C105.415,234.994,102.818,241.57,101.505,248.654z">
                        </path>
                    </g>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي السائقين</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $drivers->count() }}</h4>
            </div>
        </div>

        <div
            class=" flex  m:hidden flex-col items-start justify-between rounded-xl p-4  transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>

        <div
            class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>

        <div
            class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>
    </div>
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
                                                رقم السائق
                                            </p>
                                        </div>
                                    </th>
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
                                                السيارة
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
                                @forelse ($drivers as $driver)
                                    <tr>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $loop->iteration }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <div class="flex items-center gap-3" x-data="{ driverStatus: true }">
                                                    <div class="relative mx-2 h-10 w-10 flex-shrink-0">
                                                        @if ($driver->driver_image)
                                                            <img src="{{ url("$driver->driver_image") }}" alt="User"
                                                                class="h-full w-full rounded-full object-cover" />
                                                        @else
                                                            <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                                                alt="User" class="h-full w-full rounded-full object-cover" />
                                                        @endif
                                                        @if ($driver->is_online)
                                                            <span
                                                                class="bg-success-500 absolute right-0 bottom-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"></span>
                                                        @else
                                                            <span
                                                                class="absolute right-0 bottom-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"></span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <span
                                                        class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                        {{ $driver->name }}
                                                    </span>
                                                    <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                                                        {{ $driver->phone }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    @if ($driver->whatsapp_number)
                                                        {{ $driver->whatsapp_number }}
                                                    @else
                                                        لا يوجد
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $driver->vehicle->type }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="flex items-center">
                                                @if ($driver->is_active)
                                                    <p
                                                        class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                                        متاح
                                                    </p>
                                                @else
                                                    <p
                                                        class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                                        غير متاح
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div x-data="{ switcherToggle: {{ $driver->is_banned ? 'true' : 'false' }} }">
                                                <label :for="'toggle_{{ $driver->id }}'"
                                                    class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                                                    <div class="relative">
                                                        <input type="checkbox" :id="'toggle_{{ $driver->id }}'" class="sr-only"
                                                            x-model="switcherToggle"
                                                            @change="toggleBan({{ $driver->id }}, switcherToggle)" />
                                                        <div class="block h-6 w-11 rounded-full"
                                                            :class="switcherToggle ? 'bg-success-500' : 'bg-error-500'"></div>
                                                        <div :class="switcherToggle ? 'translate-x-full' : 'translate-x-0'"
                                                            class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear">
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center">
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                    {{ $driver->requests->count() }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 sm:px-6">
                                            <div class="flex items-center justify-center">
                                                <button onclick="window.location.href='{{ route('drivers.show', 1) }}'"
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
                                        <td colspan="8" class="p-2">
                                            <div class="flex flex-col items-center justify-center text-center">
                                                <div
                                                    class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                                    <svg fill="#dc6803" height="100px" width="100px" version="1.1" id="Capa_1"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 438.775 438.775"
                                                        xml:space="preserve">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M135.646,150.005c10.719,6.367,22.735,9.551,34.752,9.551c12.018,0,24.036-3.185,34.754-9.552 c25.497-15.148,39.324-45.114,37.937-82.214c-1.679-44.887-38.677-66.603-72.341-67.784c-0.234-0.008-0.467-0.008-0.701,0 C136.382,1.188,99.384,22.903,97.705,67.79C96.318,104.891,110.148,134.856,135.646,150.005z M170.397,19.79 c25.114,1.012,50.86,16.348,52.649,47.348h-7.381c-11.385-13-27.598-20-45.233-20c-0.001,0-0.002,0-0.003,0 c-17.672,0-33.909,7-45.3,20h-7.382C119.536,36.138,145.282,20.802,170.397,19.79z M129.925,87.138c3.188,0,6.184-1.411,8.067-3.982 c7.617-10.399,19.441-16.31,32.438-16.309c12.956,0.001,24.755,5.884,32.37,16.281c1.883,2.572,4.88,4.01,8.067,4.01h11.303 c-2.984,21-12.474,36.794-27.235,45.563c-15.136,8.992-33.939,8.938-49.075-0.054c-14.762-8.77-24.252-24.509-27.237-45.509H129.925 z M380.67,326.878c0,61.7-50.196,111.897-111.896,111.897c-5.522,0-10-4.478-10-10s4.478-10,10-10 c50.672,0,91.896-41.225,91.896-91.897c0-5.522,4.478-10,10-10S380.67,321.355,380.67,326.878z M339.234,324.878 c0-4.585-0.429-9.073-1.246-13.424c-0.028-0.172-0.062-0.343-0.099-0.515c-6.515-33.312-35.925-58.523-71.114-58.523 c-39.958,0-72.467,32.506-72.467,72.462c0,39.954,32.509,72.46,72.467,72.46C306.729,397.338,339.234,364.832,339.234,324.878z M318.802,327.059c-1.009,24.534-18.414,44.776-42.414,49.323v-41.769C291.388,333.821,305.631,331.262,318.802,327.059z M266.775,272.416c22.655,0,42.002,14.438,49.326,34.595c-14.874,5.163-31.744,7.867-49.326,7.867 c-17.581,0-34.455-2.705-49.333-7.869C224.767,286.853,244.116,272.416,266.775,272.416z M256.388,376.382 c-23-4.547-41.416-24.789-42.422-49.326c13.173,4.204,27.422,6.766,42.422,7.558V376.382z M101.505,248.654L80.128,364.138h84.43 c5.522,0,10,4.477,10,10c0,5.522-4.477,10-10,10H68.106c-2.971,0-5.788-1.393-7.688-3.677c-1.899-2.284-2.686-5.365-2.145-8.287 L81.839,244.94c5.605-30.255,27.82-52.549,60.95-61.167c17.559-4.568,36.928-4.628,54.538-0.174 c19.041,4.817,34.973,14.441,46.073,27.832c3.524,4.252,2.935,10.556-1.317,14.08c-4.253,3.527-10.556,2.935-14.08-1.316 c-16.834-20.305-49.804-28.968-80.179-21.066c-9.588,2.494-17.967,6.422-24.9,11.552l51.579,51.586 c3.905,3.905,3.905,10.236-0.001,14.142c-3.904,3.906-10.237,3.905-14.142-0.001l-51.198-51.204 C105.415,234.994,102.818,241.57,101.505,248.654z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                                                    لا يوجد سائقين حتى الآن
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                        @if($drivers->hasPages())
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">

                                    <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                        @if($drivers->onFirstPage())
                                            <button disabled
                                                class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                                                السابق
                                            </button>
                                        @else
                                            <a href="{{ $drivers->previousPageUrl() }}"
                                                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors hover:text-brand-400 hover:border-brand-400">
                                                السابق
                                            </a>
                                        @endif

                                        @php
                                            $current = $drivers->currentPage();
                                            $last = $drivers->lastPage();
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
                                            @if($page == $drivers->currentPage())
                                                <span class="p-3 py-1.5 text-sm font-medium bg-brand-500 dark:bg-brand-500 rounded-md">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ $drivers->url($page) }}"
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

                                        @if($drivers->hasMorePages())
                                            <a href="{{ $drivers->nextPageUrl() }}"
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