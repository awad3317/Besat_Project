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
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">50</h4>
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
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{$vehicle->min_price}} {{ config('app.currency_symbol', 'ر.ي') }}</h4>
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
                </tr>
              </thead>
              <!-- table header end -->
              <!-- table body start -->
              <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                <tr>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        1
                      </p>
                    </div>
                  </td>
                  <td class="px-5 py-4 sm:px-6">
                    <div class="flex items-center">
                      <div class="flex items-center gap-3" x-data="{ driverStatus: true }">
                        <div class="relative h-10 w-10 flex-shrink-0">
                          <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="User"
                            class="h-full w-full rounded-full object-cover" />
                          <span
                            class="bg-success-500 absolute right-0 bottom-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900">

                          </span>
                        </div>
                      </div>
                      <div>
                        <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
                          احمد شرجبي
                        </span>
                        <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
                          +967780236552
                        </span>
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
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">30</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          {{-- <!-- الباجينشن -->
          <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 dark:border-gray-800 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
              <a href="#"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">السابق</a>
              <a href="#"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">التالي</a>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  عرض
                  <span class="font-medium">1</span>
                  إلى
                  <span class="font-medium">10</span>
                  من
                  <span class="font-medium">97</span>
                  نتائج
                </p>
              </div>
              <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                  <a href="#"
                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:ring-gray-600 dark:hover:bg-gray-700">
                    <span class="sr-only">السابق</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd"
                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                        clip-rule="evenodd" />
                    </svg>
                  </a>
                  <a href="#" aria-current="page"
                    class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">1</a>
                  <a href="#"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700">2</a>
                  <a href="#"
                    class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700 md:inline-flex">3</a>
                  <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 dark:text-gray-300 dark:ring-gray-600">...</span>
                  <a href="#"
                    class="relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700 md:inline-flex">8</a>
                  <a href="#"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700">9</a>
                  <a href="#"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700">10</a>
                  <a href="#"
                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:ring-gray-600 dark:hover:bg-gray-700">
                    <span class="sr-only">التالي</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd"
                        d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                        clip-rule="evenodd" />
                    </svg>
                  </a>
                </nav>
              </div>
            </div>
          </div> --}}
        </div>
        <!-- ====== Table Six End -->
      </div>
    </div>
  </div>


@endsection