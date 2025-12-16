@extends('layouts.app')
@section('title', 'إدارة الطلبات الخاصة')
@section('Breadcrumb', 'إدارة الطلبات الخاصة')
@section('addButton')
  <a href="{{ route('specialOrder.create') }}"
    class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px] inline-flex items-center justify-center">
    إنشاء رحلة جديدة
  </a>
  {{-- @include('pages.or$orders.edit-vehicle-modal') --}}
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
          <svg fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $orders->count() }}</h4>
        </div>
      </div>

      <div
        class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg fill="#dc6803" width="30" height="30" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" xml:space="preserve" width="100px"
            height="100px" stroke="#dc6803" stroke-width="0.00024000000000000003">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <style type="text/css">
                .st0 {
                  fill: none;
                }
              </style>
              <path
                d="M11.7,2c-0.1,0-0.1,0-0.2,0c0,0,0,0-0.1,0v0c-0.2,0-0.3,0-0.5,0l0.2,2c0.4,0,0.9,0,1.3,0c4,0.3,7.3,3.5,7.5,7.6 c0.2,4.4-3.2,8.2-7.6,8.4c0,0-0.1,0-0.2,0c-0.3,0-0.7,0-1,0L11,22c0.4,0,0.8,0,1.3,0c0.1,0,0.3,0,0.4,0v0c5.4-0.4,9.5-5,9.3-10.4 c-0.2-5.1-4.3-9.1-9.3-9.5v0c0,0,0,0,0,0c-0.2,0-0.3,0-0.5,0C12,2,11.9,2,11.7,2z M8.2,2.7C7.7,3,7.2,3.2,6.7,3.5l1.1,1.7 C8.1,5,8.5,4.8,8.9,4.6L8.2,2.7z M4.5,5.4c-0.4,0.4-0.7,0.9-1,1.3l1.7,1C5.4,7.4,5.7,7.1,6,6.7L4.5,5.4z M15.4,8.4l-4.6,5.2 l-2.7-2.1L7,13.2l4.2,3.2l5.8-6.6L15.4,8.4z M2.4,9c-0.2,0.5-0.3,1.1-0.3,1.6l2,0.3c0.1-0.4,0.1-0.9,0.3-1.3L2.4,9z M4.1,13l-2,0.2 c0,0.1,0,0.2,0,0.3c0.1,0.4,0.2,0.9,0.3,1.3l1.9-0.6c-0.1-0.3-0.2-0.7-0.2-1.1L4.1,13z M5.2,16.2l-1.7,1.1c0.3,0.5,0.6,0.9,1,1.3 L6,17.3C5.7,16.9,5.4,16.6,5.2,16.2z M7.8,18.8l-1.1,1.7c0.5,0.3,1,0.5,1.5,0.8l0.8-1.8C8.5,19.2,8.1,19,7.8,18.8z">
              </path>
              <rect class="st0" width="24" height="24"></rect>
            </g>
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات الجارية</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $in_progressOrders }}</h4>
        </div>
      </div>

      <div
        class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg width="30" height="30" viewBox="0 0 1024 1024" fill="#dc6803" class="icon" version="1.1"
            xmlns="http://www.w3.org/2000/svg" stroke="#dc6803">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <path
                d="M824.8 1003.2H203.2c-12.8 0-25.6-2.4-37.6-7.2-11.2-4.8-21.6-12-30.4-20.8-8.8-8.8-16-19.2-20.8-30.4-4.8-12-7.2-24-7.2-37.6V260c0-12.8 2.4-25.6 7.2-37.6 4.8-11.2 12-21.6 20.8-30.4 8.8-8.8 19.2-16 30.4-20.8 12-4.8 24-7.2 37.6-7.2h94.4v48H203.2c-26.4 0-48 21.6-48 48v647.2c0 26.4 21.6 48 48 48h621.6c26.4 0 48-21.6 48-48V260c0-26.4-21.6-48-48-48H730.4v-48H824c12.8 0 25.6 2.4 37.6 7.2 11.2 4.8 21.6 12 30.4 20.8 8.8 8.8 16 19.2 20.8 30.4 4.8 12 7.2 24 7.2 37.6v647.2c0 12.8-2.4 25.6-7.2 37.6-4.8 11.2-12 21.6-20.8 30.4-8.8 8.8-19.2 16-30.4 20.8-11.2 4.8-24 7.2-36.8 7.2z"
                fill=""></path>
              <path
                d="M752.8 308H274.4V152.8c0-32.8 26.4-60 60-60h61.6c22.4-44 67.2-72.8 117.6-72.8 50.4 0 95.2 28.8 117.6 72.8h61.6c32.8 0 60 26.4 60 60v155.2m-430.4-48h382.4V152.8c0-6.4-5.6-12-12-12H598.4l-5.6-16c-12-33.6-43.2-56-79.2-56s-67.2 22.4-79.2 56l-5.6 16H334.4c-6.4 0-12 5.6-12 12v107.2zM432.8 792c-6.4 0-12-2.4-16.8-7.2L252.8 621.6c-4.8-4.8-7.2-10.4-7.2-16.8s2.4-12 7.2-16.8c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2L418.4 720c4 4 8.8 5.6 13.6 5.6s10.4-1.6 13.6-5.6l295.2-295.2c4.8-4.8 10.4-7.2 16.8-7.2s12 2.4 16.8 7.2c9.6 9.6 9.6 24 0 33.6L449.6 784.8c-4.8 4-11.2 7.2-16.8 7.2z"
                fill=""></path>
            </g>
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات المكتملة</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $completedOrders }}</h4>
        </div>
      </div>

      <div
        class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg width="30" height="30" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink" fill="#dc6803" stroke="#dc6803">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
              <title>cancelled</title>
              <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="add" fill="#dc6803" transform="translate(42.666667, 42.666667)">
                  <path
                    d="M213.333333,1.42108547e-14 C331.15408,1.42108547e-14 426.666667,95.5125867 426.666667,213.333333 C426.666667,331.15408 331.15408,426.666667 213.333333,426.666667 C95.5125867,426.666667 4.26325641e-14,331.15408 4.26325641e-14,213.333333 C4.26325641e-14,95.5125867 95.5125867,1.42108547e-14 213.333333,1.42108547e-14 Z M42.6666667,213.333333 C42.6666667,307.589931 119.076736,384 213.333333,384 C252.77254,384 289.087204,370.622239 317.987133,348.156908 L78.5096363,108.679691 C56.044379,137.579595 42.6666667,173.894198 42.6666667,213.333333 Z M213.333333,42.6666667 C173.894198,42.6666667 137.579595,56.044379 108.679691,78.5096363 L348.156908,317.987133 C370.622239,289.087204 384,252.77254 384,213.333333 C384,119.076736 307.589931,42.6666667 213.333333,42.6666667 Z"
                    id="Combined-Shape"> </path>
                </g>
              </g>
            </g>
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات الملغية</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $cancelledOrders }}</h4>
        </div>
      </div>
    </div>
  </div>

  <div
    class="w-full overflow-x-auto overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <table class="min-w-full">
      <!-- table header start -->
      <thead>
        <tr class="border-gray-100 border-y dark:border-gray-800">
          <th class="py-3">
            <div class="flex items-center">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                رقم الطلب
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                السائق
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                العميل
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center col-span-2">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                السعر
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center col-span-2">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                حالة الطلب
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center justify-center space-x-4">
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 ml-1">
                  من
                </p>
              </div>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <div class="flex items-center">
                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 ml-1">
                  الى
                </p>
              </div>
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

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse ($orders as $order)
          <tr>
            <td class="py-3">
              <div class="flex items-center">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                  1
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <div class="flex items-center gap-3">
                  <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                    @if ($order->driver->driver_image)
                      <img src="{{ url("$order->driver->driver_image") }}" alt="Driver">
                    @else
                      <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Driver" />
                    @endif
                  </div>
                  <div>
                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      {{ $order->driver->name }}
                    </p>
                    <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                      {{ $order->driver->phone }}
                    </span>
                  </div>
                </div>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <div class="flex items-center gap-3">
                  <div>
                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      @if ($order->customer_name)
                        {{ $order->customer_name }}
                      @else
                        {{$order->user->name}}
                      @endif

                    </p>
                    <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                      @if ($order->customer_phone)
                        {{ $order->customer_phone }}
                      @else
                        {{$order->user->phone}}
                      @endif

                    </span>
                  </div>
                </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                  {{ $order->price }}
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                <p class="rounded-full px-2 py-0.5 text-theme-xs font-medium {{ $order->status_class }}">
                  {{ $order->status_text }}
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center justify-center space-x-2">
                <p
                  class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                  {{ $order->start_address }}
                </p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <p
                  class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                  {{ $order->end_address }}
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center justify-center">
                <button
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
                <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                  <svg :class="'menu-item-icon-active'" class="ml-10" fill="#dc6803" width="30" height="30"
                    viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                      <defs>
                        <style>
                          .cls-1 {
                            fill: none;
                          }
                        </style>
                      </defs>
                      <title>request</title>
                      <path d="M22,22v6H6V4H16V2H6A2,2,0,0,0,4,4V28a2,2,0,0,0,2,2H22a2,2,0,0,0,2-2V22Z"
                        transform="translate(0)"></path>
                      <path
                        d="M29.54,5.76l-3.3-3.3a1.6,1.6,0,0,0-2.24,0l-14,14V22h5.53l14-14a1.6,1.6,0,0,0,0-2.24ZM14.7,20H12V17.3l9.44-9.45,2.71,2.71ZM25.56,9.15,22.85,6.44l2.27-2.27,2.71,2.71Z"
                        transform="translate(0)"></path>
                      <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1" width="32"
                        height="32">
                      </rect>
                    </g>
                  </svg>
                </div>
                <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                  لا توجد طلبات خاصة حتى الآن
                </p>
              </div>
            </td>
          </tr>
        @endforelse
        <!-- table body end -->
      </tbody>
    </table>
    @if($orders->hasPages())
      <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0">

          <div class="flex items-center space-x-1 rtl:space-x-reverse">
            @if($orders->onFirstPage())
              <button disabled
                class="px-3 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-600 rounded-md cursor-not-allowed">
                السابق
              </button>
            @else
              <a href="{{ $orders->previousPageUrl() }}"
                class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors hover:text-brand-400 hover:border-brand-400">
                السابق
              </a>
            @endif

            @php
              $current = $orders->currentPage();
              $last = $orders->lastPage();
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
              @if($page == $orders->currentPage())
                <span class="p-3 py-1.5 text-sm font-medium bg-brand-500 dark:bg-brand-500 rounded-md">
                  {{ $page }}
                </span>
              @else
                <a href="{{ $orders->url($page) }}"
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

            @if($orders->hasMorePages())
              <a href="{{ $orders->nextPageUrl() }}"
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

@endsection

@section('script')

@endsection