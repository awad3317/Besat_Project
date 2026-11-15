@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'الصفحة الرئيسية')
@section('addButton')
  <a href="{{ route('request.create') }}"
    class="bg-brand-500 hover:bg-brand-50 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px] inline-flex items-center justify-center">
    إنشاء رحلة جديدة
  </a>



  <x-modals.success-modal />
  <x-modals.error-modal />

@endsection
@section('style')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    /* ===== Responsive Layout ===== */
    .chart-container {
      width: 100%;
    }

    .ratings-container {
      width: 100%;
    }


    @media (max-width: 767px) {
      .chart-container {
        width: 100%;
        margin-bottom: 1rem;
      }

      .ratings-container {
        width: 100%;
      }
    }


    @media (min-width: 768px) and (max-width: 1279px) {
      .chart-container {
        width: 65%;
      }

      .ratings-container {
        width: 35%;
      }
    }

    @media (min-width: 1280px) {
      .chart-container {
        width: 75%;
      }

      .ratings-container {
        width: 25%;
      }
    }

    /* Force modal container above everything */
    .z-99999 {
      z-index: 99999 !important;
    }

    .dispatch-modal-box {
      width: 95vw;
      max-width: 1200px;
      height: 88vh;
      max-height: 840px;
      display: flex;
      flex-direction: column;
    }

    .dispatch-split-container {
      display: flex;
      flex-direction: row;
      gap: 16px;
      flex: 1 1 0%;
      min-height: 0;
      overflow: hidden;
    }

    .dispatch-drivers-panel {
      width: 410px;
      min-width: 360px;
      max-width: 430px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      min-height: 0;
      overflow: hidden;
    }

    .dark .dispatch-drivers-panel {
      background-color: rgba(255, 255, 255, 0.03) !important;
      border-color: #1f2a37 !important;
    }

    .dispatch-filter-pill {
      transition: all 0.2s ease;
    }

    .driver-card-item {
      transition: all 0.2s ease;
    }
    .dark .driver-card-item {
      background-color: #1a2231 !important;
      border-color: #2e394b !important;
      color: rgba(255, 255, 255, 0.9) !important;
    }
    .dark .driver-card-item:hover {
      border-color: #f79009 !important;
      box-shadow: 0 4px 14px rgba(247, 144, 9, 0.15);
    }

    .dispatch-map-panel {
      flex: 1 1 0%;
      position: relative;
      min-height: 0;
      border-radius: 16px;
      overflow: hidden;
    }

    #dispatch-map {
      width: 100%;
      height: 100%;
      position: absolute;
      inset: 0;
    }

    @media (max-width: 860px) {
      .dispatch-modal-box {
        width: 100vw;
        height: 94vh;
        max-height: none;
      }
      .dispatch-split-container {
        flex-direction: column-reverse;
      }
      .dispatch-drivers-panel {
        width: 100%;
        max-width: none;
        height: 48%;
      }
      .dispatch-map-panel {
        height: 52%;
      }
    }

    @keyframes radar-ripple {
      0% {
        transform: scale(0.6);
        opacity: 0.9;
      }
      70% {
        transform: scale(2.2);
        opacity: 0;
      }
      100% {
        transform: scale(2.4);
        opacity: 0;
      }
    }

    .pickup-pulse-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .pickup-pulse-ring {
      position: absolute;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background-color: rgba(16, 185, 129, 0.35);
      animation: radar-ripple 2.2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    }

    .driver-map-marker {
      background: none !important;
      border: none !important;
      transition: transform 0.2s ease;
    }
    .driver-map-marker:hover {
      transform: scale(1.15);
      z-index: 1000 !important;
    }

    .pickup-marker-icon,
    .dropoff-marker-icon,
    .stop-marker-icon {
      background: none !important;
      border: none !important;
    }

    .map-tooltip-rtl {
      direction: rtl;
      font-family: inherit;
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border: none;
    }

    .custom-scrollbar::-webkit-scrollbar {
      width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background-color: rgba(0,0,0,0.12);
      border-radius: 10px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
      background-color: rgba(255,255,255,0.15);
    }

    .leaflet-pane { z-index: 1 !important; }
    .leaflet-top, .leaflet-bottom { z-index: 2 !important; }
  </style>
@endsection
@section('content')

  <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">

    <div
      class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M21 19.9999C21 18.2583 19.3304 16.7767 17 16.2275M15 20C15 17.7909 12.3137 16 9 16C5.68629 16 3 17.7909 3 20M15 13C17.2091 13 19 11.2091 19 9C19 6.79086 17.2091 5 15 5M9 13C6.79086 13 5 11.2091 5 9C5 6.79086 6.79086 5 9 5C11.2091 5 13 6.79086 13 9C13 11.2091 11.2091 13 9 13Z"
            stroke="#dc6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          </path>
        </svg>
      </div>
      <div class="mt-3 w-full">
        <span class="text-xs text-gray-500 dark:text-gray-400">المستخدمين</span>
        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $totalUsers }}</h4>
      </div>
    </div>

    <div
      class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
        <svg fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
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
        <span class="text-xs text-gray-500 dark:text-gray-400">السائقين</span>
        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $totalDrivers }}</h4>
      </div>
    </div>

    <div
      class="flex flex-col items-start justify-between rounded-xl  bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
        <svg fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
          <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
        </svg>
      </div>
      <div class="mt-3 w-full">
        <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات العامه</span>
        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $totalRequests }}</h4>
      </div>
    </div>

    <div
      class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500  dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
      <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
        <svg fill="#dc6803" width="30" height="30" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
          <polygon points="23.586 13 21 10.414 21 6 23 6 23 9.586 25 11.586 23.586 13"></polygon>
          <path d="M22,18a8,8,0,1,1,8-8A8.0092,8.0092,0,0,1,22,18ZM22,4a6,6,0,1,0,6,6A6.0066,6.0066,0,0,0,22,4Z">
          </path>
          <path d="M8.63,18l7,6H30V22H16.37l-7-6H4V2H2V28a2.0025,2.0025,0,0,0,2,2H30V28H4V18Z"></path>
        </svg>
      </div>
      <div class="mt-3 w-full">
        <span class="text-xs text-gray-500 dark:text-gray-400">الطلبات الخاصة</span>
        <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{$totalSpecialOrders}}</h4>
      </div>
    </div>

  </div>

  <!-- ====== Chart One Start -->

  <div class="flex flex-col lg:flex-row gap-4 mb-4">
    <div class="chart-container">
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 h-full">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            الطلبات العامه خلال <span class="text-warning-500 dark:text-warning/90"> سنة</span>
          </h3>
        </div>

        <div class="mt-4">
          <div id="chartOne" class="w-full h-64"></div>
        </div>
      </div>
    </div>


    <div class="ratings-container">
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-5 sm:pt-5 h-full">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            أعلى تقييمات
          </h3>
        </div>

        <div class="mt-4 space-y-3">
          <!-- المستخدم 1 -->
          <div class="flex gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <div
              class="relative w-12 h-12 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800 flex-shrink-0">
              <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user"
                class="object-cover w-full h-full" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 truncate">
                أحمد شرجبي (<span class="text-warning-500 dark:text-warning/90">نوها</span>)
              </h4>
              <div class="flex items-center gap-2 mt-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                  +967780236552
                </p>
                <div class="h-3 w-px bg-gray-300 dark:bg-gray-700"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  عدن
                </p>
              </div>

              <div class="flex items-center gap-1 mt-1">
                <div class="flex">
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#e5e7eb" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-gray-800 dark:text-white/90">(4.8)</span>
              </div>
            </div>
          </div>

          <!-- المستخدم 2 -->
          <div class="flex gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <div
              class="relative w-12 h-12 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800 flex-shrink-0">
              <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user"
                class="object-cover w-full h-full" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 truncate">
                محمد العبد (<span class="text-warning-500 dark:text-warning/90">فوكسي</span>)
              </h4>
              <div class="flex items-center gap-2 mt-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                  +967712345678
                </p>
                <div class="h-3 w-px bg-gray-300 dark:bg-gray-700"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  صنعاء
                </p>
              </div>
              <!-- التقييم -->
              <div class="flex items-center gap-1 mt-1">
                <div class="flex">
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#e5e7eb" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-gray-800 dark:text-white/90">(4.6)</span>
              </div>
            </div>
          </div>

          <!-- المستخدم 3 -->
          <div class="flex gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <div
              class="relative w-12 h-12 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800 flex-shrink-0">
              <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user"
                class="object-cover w-full h-full" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 truncate">
                فاطمة قاسم (<span class="text-warning-500 dark:text-warning/90">باص</span>)
              </h4>
              <div class="flex items-center gap-2 mt-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                  +967798765432
                </p>
                <div class="h-3 w-px bg-gray-300 dark:bg-gray-700"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  تعز
                </p>
              </div>
              <!-- التقييم -->
              <div class="flex items-center gap-1 mt-1">
                <div class="flex">
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#dc6803" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#e5e7eb" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg class="w-3 h-3" fill="#e5e7eb" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <span class="text-xs font-medium text-gray-800 dark:text-white/90">(4.2)</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ====== Chart One End -->

  <div
    class="overflow-visible rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          الطلبات خلال <span class="text-warning-500 dark:text-warning/90">
            24</span> ساعة
        </h3>
      </div>

      <div class="flex items-center gap-3">

      </div>
    </div>

    <div class="w-full overflow-x-auto table-responsive-container">
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
          @forelse ($recentRequests as $request)
          <tr>
            <td class="py-3">
              <div class="flex items-center">
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                  {{ $request->id }}
                </p>
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                @if($request->driver)
                <div class="flex items-center gap-3">
                  <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                    <img src="{{ $request->driver->driver_image ? url($request->driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Driver" />
                  </div>
                  <div>
                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      {{ $request->driver->name }}
                    </p>
                    <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                      {{ $request->driver->phone }}
                    </span>
                  </div>
                </div>
                @else
                  @if ($request->canAssignDriver())
                    <button type="button"
                      x-data="{ loading: false }"
                      @click="loading = true; Livewire.dispatch('open-assign-driver-modal', { requestId: {{ $request->id }} })"
                      @open-modal.window="loading = false"
                      @open-error-modal.window="loading = false"
                      :disabled="loading"
                      class="inline-flex gap-1 items-center px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-wait">
                      <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                      </svg>
                      <div x-show="loading" x-cloak
                        class="w-3.5 h-3.5 rounded-full border-2 border-gray-500 animate-spin border-t-transparent dark:border-gray-400">
                      </div>
                      <span x-show="!loading">تعيين سائق</span>
                      <span x-show="loading" x-cloak>جاري...</span>
                    </button>
                  @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        لم يتم التعيين
                    </span>
                  @endif
                @endif
              </div>
            </td>
            <td class="py-3">
              <div class="flex items-center">
                @if($request->user)
                <div class="flex items-center gap-3">
                  <div>
                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                      {{ $request->user->name }}
                    </p>
                    <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                      {{ $request->user->phone }}
                    </span>
                  </div>
                </div>
                @endif
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
                  class="rounded-full px-2 py-0.5 text-theme-xs font-medium {{ $request->status_class }}">
                  {{ $request->status_text }}
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <p
                  class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                  {{ $request->end_address }}
                </p>
              </div>
            </td>
            <td class="px-6 py-4 text-center align-middle" x-data="{ openOptions: false }">
              <div class="flex relative justify-center items-center">
                <button @click="openOptions = !openOptions" @click.away="openOptions = false"
                  class="actions-trigger-btn">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="5" r="2"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                    <circle cx="12" cy="19" r="2"></circle>
                  </svg>
                </button>

                <div x-show="openOptions" x-transition.opacity.duration.200ms x-cloak
                  class="actions-dropdown-menu">

                  <!-- عرض التفاصيل -->
                  <a href="{{ route('request.show', $request->id) }}" class="actions-dropdown-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    عرض التفاصيل
                  </a>

                  @if ($request->driver_id && $request->canAssignDriver())
                  <!-- إعادة تعيين سائق -->
                  <button type="button"
                    @click="Livewire.dispatch('open-assign-driver-modal', { requestId: {{ $request->id }} }); openOptions = false"
                    class="actions-dropdown-item text-warning-600 dark:text-warning-500 hover:bg-warning-50 dark:hover:bg-warning-950/20">
                    <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    إعادة تعيين سائق
                  </button>
                  @endif

                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">
                لا توجد طلبات حديثة.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <livewire:request.index :modal-only="true" />

@endsection

@section('script')
  <script defer src="{{ asset('tailadmin/build/bundle.js') }}"></script>
  <script>
    window.laravelChartData = {
      sales: [
        @json($monthlySales['january'] ?? 0),
        @json($monthlySales['february'] ?? 0),
        @json($monthlySales['march'] ?? 0),
        @json($monthlySales['april'] ?? 0),
        @json($monthlySales['may'] ?? 0),
        @json($monthlySales['june'] ?? 0),
        @json($monthlySales['july'] ?? 0),
        @json($monthlySales['august'] ?? 0),
        @json($monthlySales['september'] ?? 0),
        @json($monthlySales['october'] ?? 0),
        @json($monthlySales['november'] ?? 0),
        @json($monthlySales['december'] ?? 0)
      ],
      months: [
        'يناير', 'فبراير', 'مارس', 'أبريل',
        'مايو', 'يونيو', 'يوليو', 'أغسطس',
        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
      ],
      lastUpdated: @json(now()->format('Y-m-d H:i:s')),
      yAxis: {
        min: 0,
        max: 500,
        tickAmount: 6,
      },

      colors: ["#dc6803"],
      seriesName: " ",
      chartHeight: 180,
      chartType: "bar",
      columnWidth: "35%",
      borderRadius: 5,
      strokeWidth: 2,
      fontSize: '12px',
      tooltipSuffix: " ",
      showToolbar: false,
      showDataLabels: false,
      showStroke: true,
      showXAxisBorder: false,
      showXAxisTicks: false,
      showLegend: true,
      legendPosition: "top",
      legendHorizontalAlign: "left",
      showGridLines: true,
      fillOpacity: 1,
      showTooltipX: false,
      fontFamily: "Outfit, sans-serif",
      labelRotation: 0,
    };
  </script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    window.addEventListener('driver-assigned', () => {
      setTimeout(() => window.location.reload(), 2000);
    });

    if (typeof window !== 'undefined') {
      const patchLeafletTooltip = () => {
        if (typeof L !== 'undefined' && L.Tooltip && !L.Tooltip.prototype._patchedSafeUpdate) {
          const originalUpdatePosition = L.Tooltip.prototype._updatePosition;
          L.Tooltip.prototype._updatePosition = function () {
            if (!this._map) return;
            try {
              originalUpdatePosition.apply(this, arguments);
            } catch (e) {}
          };
          L.Tooltip.prototype._patchedSafeUpdate = true;
        }
      };
      patchLeafletTooltip();
      window.addEventListener('load', patchLeafletTooltip);
    }

    let dispatchMap = null;
    let dispatchMarkers = [];
    let dispatchPickupMarker = null;
    let dispatchDropoffMarker = null;
    let dispatchStopMarkers = [];
    let dispatchRadarCircle = null;
    let dispatchPickupLocation = null;

    const DEFAULT_DRIVER_AVATAR = '{{ asset("tailadmin/build/src/images/user/SO.jpg") }}';

    function safeRemoveLayer(layer) {
      if (!layer || !dispatchMap) return;
      try {
        if (typeof layer.closeTooltip === 'function') layer.closeTooltip();
        if (typeof layer.unbindTooltip === 'function') layer.unbindTooltip();
        if (typeof layer.closePopup === 'function') layer.closePopup();
        if (typeof layer.unbindPopup === 'function') layer.unbindPopup();
        if (dispatchMap.hasLayer(layer)) {
          dispatchMap.removeLayer(layer);
        }
      } catch (err) {
        console.warn('Error removing Leaflet layer:', err);
      }
    }

    function clearAllMapLayers() {
      if (dispatchMarkers && dispatchMarkers.length) {
        dispatchMarkers.forEach(m => safeRemoveLayer(m));
        dispatchMarkers = [];
      }
      if (dispatchPickupMarker) {
        safeRemoveLayer(dispatchPickupMarker);
        dispatchPickupMarker = null;
      }
      if (dispatchDropoffMarker) {
        safeRemoveLayer(dispatchDropoffMarker);
        dispatchDropoffMarker = null;
      }
      if (dispatchRadarCircle) {
        safeRemoveLayer(dispatchRadarCircle);
        dispatchRadarCircle = null;
      }
      if (dispatchStopMarkers && dispatchStopMarkers.length) {
        dispatchStopMarkers.forEach(m => safeRemoveLayer(m));
        dispatchStopMarkers = [];
      }
    }

    function assignDriverDispatch() {
      return {
        show: false,
        isLoading: false,
        isSuccess: false,
        selectedDriverId: null,
        selectedDriver: null,

        initMap() {
          if (typeof L === 'undefined') {
            setTimeout(() => this.initMap(), 150);
            return;
          }

          const mapEl = this.$refs.dispatchMap;
          if (!mapEl) return;

          if (dispatchMap) {
            try {
              dispatchMap.invalidateSize();
            } catch (e) {}
            return;
          }

          dispatchMap = L.map(mapEl, {
            center: [15.3694, 44.191],
            zoom: 13,
            zoomControl: false,
            attributionControl: false,
          });

          const googleKey = @json(config('services.google.maps_key') ?: env('GOOGLE_MAP_KEY'));
          const tileUrl = googleKey
            ? 'https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=ar&gl=YE&key=' + encodeURIComponent(googleKey)
            : 'https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=ar&gl=YE';

          L.tileLayer(tileUrl, {
            maxZoom: 20,
            subdomains: ['0', '1', '2', '3'],
            attribution: 'Google Maps'
          }).addTo(dispatchMap);

          L.control.zoom({ position: 'bottomleft' }).addTo(dispatchMap);

          setTimeout(() => {
            if (dispatchMap) dispatchMap.invalidateSize();
          }, 250);
        },

        invalidateMap() {
          if (dispatchMap) {
            try {
              dispatchMap.invalidateSize();
            } catch (e) {}
          }
        },

        syncMap(data) {
          if (!data) return;
          if (typeof L === 'undefined' || !dispatchMap) {
            setTimeout(() => this.syncMap(data), 200);
            return;
          }

          try {
            dispatchMap.stop();
          } catch (e) {}

          clearAllMapLayers();

          const bounds = L.latLngBounds();
          let hasValidPickup = false;

          if (data.pickup && data.pickup.latitude && data.pickup.longitude) {
            const pickupLatLng = [Number(data.pickup.latitude), Number(data.pickup.longitude)];
            dispatchPickupLocation = pickupLatLng;
            hasValidPickup = true;

            dispatchPickupMarker = L.marker(pickupLatLng, {
              icon: L.divIcon({
                className: 'pickup-marker-icon',
                html: '<div class="pickup-pulse-wrapper">' +
                        '<div class="pickup-pulse-ring"></div>' +
                        '<div class="flex relative z-10 justify-center items-center w-10 h-10 text-white bg-emerald-500 rounded-full border-white shadow-xl border-3">' +
                          '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>' +
                        '</div>' +
                      '</div>',
                iconSize: [40, 40],
                iconAnchor: [20, 20],
              })
            }).addTo(dispatchMap);

            if (data.pickup.address) {
              dispatchPickupMarker.bindTooltip('موقع العميل: ' + data.pickup.address, { direction: 'top', offset: [0, -20], className: 'map-tooltip-rtl' });
            }

            dispatchRadarCircle = L.circle(pickupLatLng, {
              radius: 1200,
              color: '#10b981',
              fillColor: '#10b981',
              fillOpacity: 0.08,
              weight: 2,
              dashArray: '5, 5',
            }).addTo(dispatchMap);

            bounds.extend(pickupLatLng);
          }

          if (data.dropoff && data.dropoff.latitude && data.dropoff.longitude) {
            const dropoffLatLng = [Number(data.dropoff.latitude), Number(data.dropoff.longitude)];

            dispatchDropoffMarker = L.marker(dropoffLatLng, {
              icon: L.divIcon({
                className: 'dropoff-marker-icon',
                html: '<div class="flex justify-center items-center w-9 h-9 text-white bg-red-500 rounded-full border-white shadow-xl border-3">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>' +
                      '</div>',
                iconSize: [36, 36],
                iconAnchor: [18, 18],
              })
            }).addTo(dispatchMap);

            if (data.dropoff.address) {
              dispatchDropoffMarker.bindTooltip('الوجهة: ' + data.dropoff.address, { direction: 'top', offset: [0, -18], className: 'map-tooltip-rtl' });
            }

            bounds.extend(dropoffLatLng);
          }

          if (data.stops && data.stops.length) {
            data.stops.forEach((stop, i) => {
              if (!stop.latitude || !stop.longitude) return;
              const stopLatLng = [Number(stop.latitude), Number(stop.longitude)];
              const stopMarker = L.marker(stopLatLng, {
                icon: L.divIcon({
                  className: 'stop-marker-icon',
                  html: '<div class="flex justify-center items-center w-7 h-7 text-xs font-bold text-white bg-amber-500 rounded-full border-2 border-white shadow-lg">' + (i + 1) + '</div>',
                  iconSize: [28, 28],
                  iconAnchor: [14, 14],
                })
              }).addTo(dispatchMap);
              dispatchStopMarkers.push(stopMarker);
              bounds.extend(stopLatLng);
            });
          }

          let nearbyDriversCount = 0;
          if (data.drivers && data.drivers.length) {
            data.drivers.forEach(driver => {
              if (!driver.latitude || !driver.longitude) return;
              const driverLatLng = [Number(driver.latitude), Number(driver.longitude)];

              const distBadge = driver.distance_km !== null
                ? '<div class="bg-gray-900/90 text-white font-bold text-[10px] px-2 py-0.5 rounded-full shadow-md whitespace-nowrap mb-1 inline-block">' + driver.distance_km + ' كم</div>'
                : '';

              const onlineRing = driver.is_online ? 'border-emerald-500 ring-2 ring-emerald-400' : 'border-gray-300';
              const onlineDot = driver.is_online
                ? '<div class="absolute -right-0.5 -bottom-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white"></div>'
                : '<div class="absolute -right-0.5 -bottom-0.5 w-3.5 h-3.5 bg-gray-400 rounded-full border-2 border-white"></div>';

              let avatarSrc = driver.driver_image || '';
              if (!avatarSrc || avatarSrc.includes('default_driver1.png')) {
                avatarSrc = DEFAULT_DRIVER_AVATAR;
              }

              const marker = L.marker(driverLatLng, {
                icon: L.divIcon({
                  className: 'driver-map-marker',
                  html: '<div class="flex flex-col items-center cursor-pointer">' +
                          distBadge +
                          '<div class="relative">' +
                            '<img src="' + avatarSrc + '" class="w-10 h-10 rounded-full object-cover border-2 shadow-lg ' + onlineRing + '" onerror="this.onerror=null;this.src=\'' + DEFAULT_DRIVER_AVATAR + '\';" />' +
                            onlineDot +
                          '</div>' +
                        '</div>',
                  iconSize: [44, 56],
                  iconAnchor: [22, 50],
                })
              }).addTo(dispatchMap);

              marker.on('click', () => {
                this.selectDriver(driver);
              });

              dispatchMarkers.push(marker);

              if (driver.distance_km !== null && driver.distance_km <= 35) {
                bounds.extend(driverLatLng);
                nearbyDriversCount++;
              }
            });
          }

          try {
            if (hasValidPickup) {
              if (nearbyDriversCount > 0 && bounds.isValid()) {
                dispatchMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 15, animate: false });
              } else {
                dispatchMap.setView(dispatchPickupLocation, 14, { animate: false });
              }
            } else if (bounds.isValid()) {
              dispatchMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 14, animate: false });
            }
          } catch (err) {
            console.warn('Error adjusting map bounds:', err);
          }
        },

        selectDriver(driver) {
          this.selectedDriverId = driver.id;
          this.selectedDriver = driver;

          if (driver.latitude && driver.longitude && dispatchMap) {
            try {
              dispatchMap.stop();
              dispatchMap.flyTo([Number(driver.latitude), Number(driver.longitude)], 15, { duration: 0.8 });
            } catch (e) {
              dispatchMap.setView([Number(driver.latitude), Number(driver.longitude)], 15);
            }
          }
        },

        recenterOnPickup() {
          if (dispatchPickupLocation && dispatchMap) {
            try {
              dispatchMap.stop();
              dispatchMap.flyTo(dispatchPickupLocation, 14, { duration: 0.7 });
            } catch (e) {
              dispatchMap.setView(dispatchPickupLocation, 14);
            }
          }
        },

        clearSelection() {
          this.selectedDriverId = null;
          this.selectedDriver = null;
        },

        flyToDriver(lat, lng, driverId, driverData) {
          this.selectDriver(driverData);
        },

        estimateArrival(distanceKm) {
          const km = parseFloat(distanceKm);
          if (!km || isNaN(km) || km <= 0) return '—';

          const avgSpeedKmPerMin = 0.5;
          const mins = Math.round(km / avgSpeedKmPerMin);

          if (mins < 1) return 'أقل من دقيقة';
          if (mins === 1) return 'دقيقة واحدة';
          if (mins === 2) return 'دقيقتان';

          if (mins < 60) {
            if (mins <= 10) return mins + ' دقائق';
            return mins + ' دقيقة';
          }

          const hours = Math.floor(mins / 60);
          const remMins = mins % 60;

          let hourStr = '';
          if (hours === 1) hourStr = 'ساعة';
          else if (hours === 2) hourStr = 'ساعتان';
          else if (hours >= 3 && hours <= 10) hourStr = hours + ' ساعات';
          else hourStr = hours + ' ساعة';

          if (remMins === 0) {
            return hourStr;
          }

          let minStr = '';
          if (remMins === 1) minStr = 'دقيقة واحدة';
          else if (remMins === 2) minStr = 'دقيقتان';
          else if (remMins <= 10) minStr = remMins + ' دقائق';
          else minStr = remMins + ' دقيقة';

          return hourStr + ' و ' + minStr;
        }
      };
    }
  </script>

@endsection