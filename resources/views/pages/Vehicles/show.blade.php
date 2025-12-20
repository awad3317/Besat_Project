@extends('layouts.app')
@section('title', 'تفاصيل المركبه')
@section('Breadcrumb', "تفاصيل المركبة")
@section('addButton')
  @include('pages.VehiclePricing.edit-vehicle-pricing-modal')
  <x-modals.success-modal />
  <x-modals.error-modal />
@endsection
@section('style')

@endsection
@section('content')

  <div class=" mx-auto max-w-(--breakpoint-2xl)">
    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
      <div
        class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg fill="#dc6803" width="24" height="24" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <rect x="27" y="11" width="2" height="4"></rect>
            <rect x="3" y="11" width="2" height="4"></rect>
            <rect x="20" y="20" width="2" height="2"></rect>
            <rect x="10" y="20" width="2" height="2"></rect>
            <path
              d="M21,4H11A5.0059,5.0059,0,0,0,6,9V23a2.0023,2.0023,0,0,0,2,2v3h2V25H22v3h2V25a2.0027,2.0027,0,0,0,2-2V9A5.0059,5.0059,0,0,0,21,4Zm3,6,.0009,6H8V10ZM11,6H21a2.995,2.995,0,0,1,2.8157,2H8.1843A2.995,2.995,0,0,1,11,6ZM8,23V18H24.0012l.0008,5Z"
              transform="translate(0 0)"></path>
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي السائقين</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $vehicle->drivers->count() }}</h4>
        </div>
      </div>

      <div
        class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg fill="#dc6803" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">أقل سعر للمركبة</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{$vehicle->min_price}}
            {{ config('app.currency_symbol', 'ر.ي') }}
          </h4>
        </div>
      </div>

      <div
        class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

      </div>

      <div
        class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

      </div>
    </div>
  </div>
  <div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 mb-4">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          الاسعار <span class="text-warning-500 dark:text-warning/90">
            بالكيلو</span> متر
        </h3>
      </div>
      <div class="flex items-center gap-3">
        @include('pages.VehiclePricing.create-vehicle-pricing-modal')
      </div>
    </div>
    <div class="w-full overflow-x-auto">
      <!-- table start -->
      <table class="min-w-full">
        <!-- table header start -->
        <thead>
          <tr class="border-gray-100 border-y dark:border-gray-800">
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  رقم
                </p>
              </div>
            </th>
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  من
                </p>
              </div>
            </th>
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  الى
                </p>
              </div>
            </th>
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  السعر لكل كيلو متر
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
          @foreach ($vehicle->pricing as $index => $price)
            <tr>
              <td class="py-3">
                <div class="flex items-center">
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                    {{ $index + 1 }}
                  </p>
                </div>
              </td>
              <td class="py-3">
                <div class="flex items-center">
                  <div class="flex items-center gap-3">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      {{ number_format($price->min_distance_km, 2) }} كم
                    </p>
                  </div>
              </td>
              <td class="py-3">
                <div class="flex items-center">
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                    {{ number_format($price->max_distance_km, 2) }} كم
                  </p>
                </div>
              </td>
              <td class="py-3">
                <div class="flex items-center">
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                    {{ number_format($price->base_price, 2) }} {{ config('app.currency_symbol', 'ر.ي') }}
                  </p>
                </div>
              </td>
              <td class="py-3">
                <div class="flex items-center justify-center">
                  <div class="flex items-center justify-center gap-1">

                    <button onclick="window.location.href='{{ route('vehiclePricing.edit', $price->id) }}'"
                      class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>

                    @if($loop->last)
                      <form action="{{ route('vehiclePricing.destroy', $price->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('هل أنت متأكد من حذف هذا السعر؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                          class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </form>
                    @endif
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
          <!-- table body end -->
        </tbody>
        <!-- table body end -->
      </table>
      <!-- table end -->
    </div>
  </div>
  <div class="space-y-5 sm:space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="px-5 py-4 sm:px-6 sm:py-5">

        <div class="flex flex-col sm:flex-row gap-4 items-end">
          <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
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
                        رقم
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
                        عدد الطلبات
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
                @forelse ($vehicle->drivers as $driver)
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
                              <img src="{{ url("$driver->driver_image") }}" alt="User" loading="lazy" width="40" height="40"
                                class="h-full w-full rounded-full object-cover" />
                            @else
                              <img src="{{ asset('assets/img/User_img.png') }}" alt="User" loading="lazy" width="40"
                                height="40" class="h-full w-full rounded-full object-cover" />
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
                          <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
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
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $driver->requests->count() }}</p>
                      </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                      <div class="flex items-center justify-center">
                        <a href="{{ route('drivers.show', $driver->id) }}"
                          class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          تفاصيل
                        </a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="p-8 text-center">
                      <div class="flex flex-col items-center justify-center text-center">
                        <div
                          class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                          <svg fill="#dc6803" height="100px" width="100px" version="1.1" id="Capa_1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            viewBox="0 0 438.775 438.775" xml:space="preserve">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
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
          </div>
        </div>
        <!-- ====== Table Six End -->
      </div>
    </div>
  </div>


@endsection