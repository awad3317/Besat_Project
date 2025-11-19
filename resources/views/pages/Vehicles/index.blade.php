@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'إدارة المركبات')
@section('addButton')
    <button class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px]">
        <span>إضافة مركبه</span>
    </button>
@endsection
@section('style')

@endsection
@section('content')

  <div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          المركبات
        </h3>
      </div>

      <div class="flex items-center gap-3">

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
                  رقم المركبة
                </p>
              </div>
            </th>
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  الصورة
                </p>
              </div>
            </th>
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  النوع
                </p>
              </div>
            </th>
            <th class="py-3">
              <div class="flex items-center col-span-2">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  الوصف
                </p>
              </div>
            </th>
            <th>
                <div class="flex items-center">
                  <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                    عدد الاشخاص
                  </p>
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
            @foreach($vehicles as $vehicle)
          <tr>
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
                  <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                    <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                  </div>
                </div>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <div class="flex items-center gap-3">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                  {{ $vehicle->name }}
                </p>
                </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                  {{ $vehicle->description }}
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                  {{ $vehicle->max_passengers }}
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center justify-center">
                <button onclick="window.location.href='{{ route('Vehicle.show', $vehicle->id ) }}'"
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
            @endforeach
          <!-- table body end -->
        </tbody>
        <!-- table body end -->
      </table>
      <!-- table end -->
    </div>
  </div>

@endsection

@section('script')

@endsection
