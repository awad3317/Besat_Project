@extends('layouts.app')
@section('title', 'تفاصيل المستخدم')
@section('Breadcrumb', 'تفاصيل المستخدم')
@section('addButton')

@endsection
@section('style')

@endsection
@section('content')
  <x-modals.success-modal />
  <x-modals.error-modal />

  <div x-data="{ isAddBalanceModalOpen: @if($errors->has('amount')) true @else false @endif }" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
      <div
        class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $user->requests->count() }}</h4>
        </div>
      </div>

      <div
        class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg fill="none" stroke="#d97706" stroke-width="1.5" width="20" height="20" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.787 1.4 8.168L12 18.896l-7.334 3.857 1.4-8.168L.132 9.21l8.2-1.192z" />
          </svg>
        </div>
        <div class="mt-3 w-full">
          <span class="text-xs text-gray-500 dark:text-gray-400">نقاط الولاء</span>
          <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
            {{ $user->loyalty_points ?? 0 }}
          </h4>
        </div>
      </div>
      <div
        class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg fill="#d97706" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M21 7H3c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 12H3V9h18v10zm-4-4c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" />
          </svg>
        </div>
        <div class="mt-3 w-full flex items-end justify-between">
          <div>
            <span class="text-xs text-gray-500 dark:text-gray-400">رصيد المحفظة</span>
            <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">
              {{ number_format($user->wallet_balance ?? 0, 2) }}
            </h4>
          </div>
          <button @click="isAddBalanceModalOpen = true" class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 text-white hover:bg-brand-600 transition-colors shadow-sm" title="إضافة رصيد">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
          </button>
        </div>
      </div>

      <div
        class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

      </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">


        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between">

          <div class="flex flex-col w-full xl:flex-row xl:justify-between">
            <div class="flex gap-6">
              <div>
                <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                  {{ $user->name }}
                </h4>
                <div class="flex items-center gap-3">
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->phone }}
                  </p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
        <div class=" mb-6">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            <span class="text-warning-500 dark:text-warning/90">الطلبات</span>
          </h3>
        </div>
        <div class="w-full overflow-x-auto">
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
                  <div class="flex items-center col-span-2">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                      تاريخ الطلب
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
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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
              @forelse ($user->requests as $request)
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
                          @if ($request->driver && $request->driver->driver_image)
                            <img src='{{ url("$request->driver->driver_image") }}' alt="Driver Image" loading="lazy" />
                          @else
                            <img src="{{ asset('assets/img/User_img.png') }}" alt="Driver Image" loading="lazy" />
                          @endif
                        </div>
                        <div>
                          @if($request->driver)
                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                              {{ $request->driver->name }}
                            </p>
                            <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                              {{ $request->driver->phone }}
                            </span>
                          @else
                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                              غير معين
                            </p>
                          @endif
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="py-3">
                    <div class="flex items-center">
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        {{ $request->final_price }}
                      </p>
                    </div>
                  </td>
                  <td class="py-3">
                    <div class="flex items-center">
                      <p
                        class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                        {{ $request->status }}
                      </p>
                    </div>
                  </td>
                  <td class="py-3">
                    <div class="flex items-center">
                      <p
                        class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                        {{ $request->created_at->format('Y/m/d') }}
                      </p>
                    </div>
                  </td>
                  <td class="py-3">
                    <div class="flex items-center justify-center space-x-2">
                      <p
                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                        {{ $request->start_address }}
                      </p>
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                      </svg>
                      <p
                        class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                        {{ $request->end_address }}
                      </p>
                    </div>
                  </td>
                  <td class="py-3">
                    <div class="flex items-center justify-center">
                      <a href="{{ route('request.show', $request->id) }}" class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs
                                                        font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800
                                                        dark:text-white">
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
                  <td colspan="8" class="p-2">
                    <div class="flex flex-col items-center justify-center text-center">
                      <div
                        class="h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                        <svg class="ml-10" fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" id="icon"
                          xmlns="http://www.w3.org/2000/svg">
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
                            <rect id="_Transparent_Rectangle_" data-name="&lt;Transparent Rectangle&gt;" class="cls-1"
                              width="32" height="32">
                            </rect>
                          </g>
                        </svg>
                      </div>
                      <p class="text-gray-500 text-theme-sm dark:text-gray-400 font-medium">
                        لا توجد طلبات حتى الآن
                      </p>
                    </div>
                  </td>
                </tr>
              @endforelse
              {{-- <!-- 1 -->
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
                        <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                      </div>
                      <div>
                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                          أحمد شرجبي
                        </p>
                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                          +967780236552
                        </span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      9700
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p
                      class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                      ملغية
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p
                      class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                      2025/5/3
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center justify-center space-x-2">
                    <p
                      class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                      المنصورة
                    </p>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <p
                      class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                      التواهي
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
              </tr> --}}

              {{-- <!-- 2 -->
              <tr>
                <td class="py-3">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      2
                    </p>
                  </div>
                </td>

                <td class="py-3">
                  <div class="flex items-center">
                    <div class="flex items-center gap-3">
                      <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                        <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                      </div>
                      <div>
                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                          أحمد شرجبي
                        </p>
                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                          +967780236552
                        </span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      8500
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p
                      class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                      مكتملة
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p
                      class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                      2025/5/3
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center justify-center space-x-2">
                    <p
                      class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                      خورمكسر
                    </p>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <p
                      class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                      المعلا
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
              </tr> --}}

              <!-- 3 -->
              {{-- <tr>
                <td class="py-3">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      3
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <div class="flex items-center gap-3">
                      <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                        <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                      </div>
                      <div>
                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                          أحمد شرجبي
                        </p>
                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                          +967780236552
                        </span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                      10000
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p
                      class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                      بالانتظار
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center">
                    <p
                      class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                      2025/5/3
                    </p>
                  </div>
                </td>
                <td class="py-3">
                  <div class="flex items-center justify-center space-x-2">
                    <p
                      class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                      المنصورة
                    </p>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <p
                      class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                      عدن
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
              </tr> --}}
              <!-- table body end -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- الحركات في آخر الصفحة --}}
    <div x-data="{ currentTab: null }" class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mt-6">
        
        {{-- كرت حركات المحفظة --}}
        <div :class="currentTab === 'wallet' ? 'border-brand-500 dark:border-brand-500' : 'border-gray-200 dark:border-gray-800'" class="rounded-2xl border bg-white dark:bg-white/[0.03] overflow-hidden transition-all duration-300">
            <button @click="currentTab = currentTab === 'wallet' ? null : 'wallet'" class="w-full flex items-center justify-between p-5 lg:p-6 bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-right">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800 text-warning-500 dark:text-warning-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">حركات المحفظة الأخيرة</h3>
                </div>
                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="currentTab === 'wallet' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="currentTab === 'wallet'" x-collapse x-cloak class="border-t border-gray-100 dark:border-gray-800 p-4 lg:p-5 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="pb-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">النوع</th>
                            <th class="pb-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">المبلغ</th>
                            <th class="pb-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($user->walletTransactions ?? [] as $transaction)
                            <tr>
                                <td class="py-3 text-theme-sm text-gray-600 dark:text-gray-400">{{ $transaction->type_text }}</td>
                                <td class="py-3 text-theme-sm font-semibold {{ $transaction->amount > 0 ? 'text-success-600' : 'text-error-600' }}">
                                    {{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="py-3 text-theme-sm text-gray-400">{{ $transaction->created_at->format('Y/m/d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-theme-sm text-gray-400">لا توجد عمليات محفظة حالياً.</td>
                            </tr>
                        @endempty
                    </tbody>
                </table>
            </div>
        </div>

        {{-- كرت حركات نقاط الولاء --}}
        <div :class="currentTab === 'points' ? 'border-brand-500 dark:border-brand-500' : 'border-gray-200 dark:border-gray-800'" class="rounded-2xl border bg-white dark:bg-white/[0.03] overflow-hidden transition-all duration-300">
            <button @click="currentTab = currentTab === 'points' ? null : 'points'" class="w-full flex items-center justify-between p-5 lg:p-6 bg-gray-50/50 dark:bg-gray-800/20 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-right">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 dark:bg-gray-800 text-warning-500 dark:text-warning-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.151-.316.63-.316.78 0l2.316 4.67 5.143.747c.348.05.488.48.236.726l-3.721 3.626 1.057 5.123c.072.348-.29.612-.598.448L12 16.591l-4.591 2.413c-.309.163-.671-.1-.599-.448l1.057-5.122-3.721-3.627c-.252-.246-.112-.676.236-.726l5.143-.747 2.316-4.67z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">سجل نقاط الولاء</h3>
                </div>
                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="currentTab === 'points' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="currentTab === 'points'" x-collapse x-cloak class="border-t border-gray-100 dark:border-gray-800 p-4 lg:p-5 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="pb-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">الحدث / السبب</th>
                            <th class="pb-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">النقاط</th>
                            <th class="pb-3 text-right font-medium text-gray-500 text-theme-xs dark:text-gray-400">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($user->pointsTransactions ?? [] as $point)
                            <tr>
                                <td class="py-3 text-theme-sm text-gray-600 dark:text-gray-400">{{ $point->description }}</td>
                                <td class="py-3 text-theme-sm font-semibold {{ $point->points > 0 ? 'text-warning-600' : 'text-gray-500' }}">
                                    {{ $point->points > 0 ? '+' : '' }}{{ $point->points }}
                                </td>
                                <td class="py-3 text-theme-sm text-gray-400">{{ $point->created_at->format('Y/m/d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-theme-sm text-gray-400">لا توجد نقاط مسجلة حالياً.</td>
                            </tr>
                        @endempty
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- مودال إضافة رصيد للمحفظة --}}
    <div x-show="isAddBalanceModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
        style="display: none;" x-transition x-cloak>
        
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="isAddBalanceModalOpen = false">
        </div>

        <div @click.outside="isAddBalanceModalOpen = false"
            class="relative w-full rounded-2xl bg-white p-5 dark:bg-gray-900 shadow-xl z-10"
            style="max-width: 400px;">

            <form method="POST" action="{{ route('users.add-balance', $user->id) }}" x-data="{ isLoading: false }" @submit="isLoading = true">
                @csrf
                
                <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90 text-right font-semibold">
                    شحن محفظة المستخدم
                </h4>

                <div class="space-y-4 text-right">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-500 dark:text-gray-400">
                            المستخدم
                        </label>
                        <div class="font-semibold text-gray-800 dark:text-white/90">
                            {{ $user->name }} ({{ $user->phone }})
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            المبلغ المطلوب إضافته <span class="text-xs text-warning-500">*</span>
                        </label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount" required placeholder="0.00"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white text-right">
                        @error('amount') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="isAddBalanceModalOpen = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 dark:bg-gray-800 dark:text-white dark:border-gray-600 sm:w-auto">
                        إلغاء
                    </button>
                    
                    <button type="submit" :disabled="isLoading"
                        class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all sm:w-auto">
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" style="display: none;"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'جاري الإضافة...' : 'تأكيد الشحن'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>
@endsection
@section('script')

@endsection