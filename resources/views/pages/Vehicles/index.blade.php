@extends('layouts.app')
@section('title', 'إدارة المركبات')
@section('Breadcrumb', 'إدارة المركبات')
@section('addButton')
  @include('pages.Vehicles.create-vehicle-modal')
  @include('pages.Vehicles.edit-vehicle-modal')
  <x-modals.success-modal />
  <x-modals.error-modal />
  <x-modals.delete-modal />

@endsection
@section('style')

@endsection
@section('content')
  <div class=" mx-auto max-w-(--breakpoint-2xl)">
    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
      <div
        class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
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
          <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المركبات</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $vehicles->count() }}</h4>
        </div>
      </div>

      <div
        class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

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
            <th>
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  أقل سعر
                </p>
            </th>
            <th class="py-3">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                  الحالة
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
          @forelse($vehicles as $vehicle)
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
                      @if($vehicle->image)
                        <img src="{{ url($vehicle->image) }}" alt="Vehicle Image" class="h-full w-full object-cover" />
                      @else
                        <img src="{{ asset('assets/img/Car_img.png') }}" alt="Vehicle Image"
                          class="h-full w-full object-cover" />
                      @endif
                    </div>
                  </div>
                </div>
              </td>
              <td class="py-3">
                <div class="flex items-center">
                  <div class="flex items-center gap-3">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      {{ $vehicle->type }}
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
                <div class="flex items-center">
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                    {{ $vehicle->min_price }} {{ config('app.currency_symbol', 'ر.ي') }}
                  </p>
                </div>
              </td>
              <td class="py-3">
                <div class="flex items-center">
                  @if($vehicle->trashed())
                    <p class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                      محذوفة
                    </p>
                  @else
                    <p class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                      نشطة
                    </p>
                  @endif
                </div>
              </td>

              <td class="py-3">
                <div class="flex items-center justify-center gap-2">
                  
                  @if(!$vehicle->trashed())
                    <button type="button" onclick="window.location.href='{{ route('Vehicle.show', $vehicle->id) }}'"
                      class="flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-2 text-gray-500 transition-all hover:border-blue-500 hover:text-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-blue-400 dark:hover:text-blue-400 dark:hover:bg-gray-700">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>

                    <div x-data="{ isLoading: false }">
                      <button @click="isLoading = true; window.location.href='{{ route('Vehicle.edit', $vehicle->id) }}'"
                        :disabled="isLoading"
                        class="relative flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-2 text-gray-500 transition-all hover:border-green-500 hover:text-green-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-green-400 dark:hover:text-green-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="isLoading" class="animate-spin w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                      </button>
                    </div>

                    <button type="button" 
                      @click="$dispatch('open-delete-modal', { action: '{{ route('Vehicle.destroy', $vehicle->id) }}' })"
                      class="flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-2 text-gray-500 transition-all hover:border-red-500 hover:text-red-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-red-400 dark:hover:text-red-400 dark:hover:bg-gray-700">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>

                  @else
                    <form action="{{ route('Vehicle.restore', $vehicle->id) }}" method="POST" class="m-0" onsubmit="return confirm('هل أنت متأكد من استعادة هذه المركبة للعمل؟');">
                      @csrf
                      <button type="submit" class="flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-2 text-gray-500 transition-all hover:border-emerald-500 hover:text-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-emerald-400 dark:hover:text-emerald-400 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                      </button>
                    </form>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-2">
                <div class="flex flex-col items-center justify-center text-center">
                  <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" width="24" height="24" viewBox="0 0 24 24"
                      fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M22.875 8.625L21.75 9L21 10.5L19.1776 4.4253C18.9238 3.57933 18.1452 3 17.2619 3H6.73806C5.85484 3 5.0762 3.57934 4.82241 4.4253L3 10.5L2.25 9L1.125 8.625"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path
                        d="M1.5 19.468V21C1.5 21.3978 1.65804 21.7794 1.93934 22.0607C2.22064 22.342 2.60218 22.5 3 22.5C3.39782 22.5 3.77936 22.342 4.06066 22.0607C4.34196 21.7794 4.5 21.3978 4.5 21V19.555"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path
                        d="M22.5 19.468V21C22.5 21.3978 22.342 21.7794 22.0607 22.0607C21.7794 22.342 21.3978 22.5 21 22.5C20.6022 22.5 20.2206 22.342 19.9393 22.0607C19.658 21.7794 19.5 21.3978 19.5 21V19.5766"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path
                        d="M2.75 10.5H12H21.25C21.7804 10.5 22.2891 10.7299 22.6642 11.139C23.0393 11.5482 23.25 12.1032 23.25 12.6818V18.4091C23.25 18.6984 23.1446 18.9759 22.9571 19.1805C22.7696 19.3851 22.5152 19.5 22.25 19.5H1.75C1.48478 19.5 1.23043 19.3851 1.04289 19.1805C0.855357 18.9759 0.75 18.6984 0.75 18.4091V12.6818C0.75 12.1032 0.960714 11.5482 1.33579 11.139C1.71086 10.7299 2.21957 10.5 2.75 10.5Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M0.75 14.25H5L6.125 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                      <path d="M14.25 16.5H9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                      <path d="M23.25 14.25H19L17.875 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    </svg>
                  </div>
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                    لا توجد مركبات حتى الآن
                  </p>
                </div>
              </td>
            </tr>
          @endforelse
          <!-- table body end -->
        </tbody>
        <!-- table body end -->
      </table>
      <!-- table end -->
      @if($vehicles->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">

            <div class="flex items-center space-x-1 rtl:space-x-reverse">
              @if($vehicles->onFirstPage())
                <button disabled
                  class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                  السابق
                </button>
              @else
                <a href="{{ $vehicles->previousPageUrl() }}"
                  class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors hover:text-brand-400 hover:border-brand-400">
                  السابق
                </a>
              @endif

              @php
                $current = $vehicles->currentPage();
                $last = $vehicles->lastPage();
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
                @if($page == $vehicles->currentPage())
                  <span class="p-3 py-1.5 text-sm font-medium bg-brand-500 dark:bg-brand-500 rounded-md">
                    {{ $page }}
                  </span>
                @else
                  <a href="{{ $vehicles->url($page) }}"
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

              @if($vehicles->hasMorePages())
                <a href="{{ $vehicles->nextPageUrl() }}"
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