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

  <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
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
        class=" flex  m:hidden flex-col items-start justify-between rounded-xl p-4  transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

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
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
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
                  السعر
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
                    <!-- زر التعديل -->
                    <button onclick="window.location.href='{{ route('vehiclePricing.edit', $price->id) }}'"
                      class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>

                    <!-- زر الحذف -->
                    <form action="{{ route('vehiclePricing.destroy', $price->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا السعر؟')">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                        class="flex items-center gap-1 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-red-700 transition-all hover:bg-red-50 dark:border-red-600 dark:bg-gray-800 dark:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </form>
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

@endsection