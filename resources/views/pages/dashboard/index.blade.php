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
  </style>
@endsection
@section('content')

  <div class="flex flex-col flex-wrap gap-4 mb-4 sm:flex-row md:gap-6">

    <div
      class="flex flex-col items-start justify-between rounded-xl bg-white p-4 border border-brand-500 dark:border-brand-500 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
      <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
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
      <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
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
      <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
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
      <div class="flex justify-center items-center w-10 h-10 bg-gray-100 rounded-lg dark:bg-gray-800">
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

  <div class="flex flex-col gap-4 mb-4 lg:flex-row">
    <div class="chart-container">
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 h-full">
        <div class="flex justify-between items-center">
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
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            أعلى تقييمات
          </h3>
        </div>

        <div class="mt-4 space-y-3">
          <!-- المستخدم 1 -->
          <div class="flex gap-3 p-3 rounded-lg transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
            <div
              class="overflow-hidden relative flex-shrink-0 w-12 h-12 rounded-full border border-gray-200 dark:border-gray-800">
              <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user"
                class="object-cover w-full h-full" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-800 truncate dark:text-white/90">
                أحمد شرجبي (<span class="text-warning-500 dark:text-warning/90">نوها</span>)
              </h4>
              <div class="flex gap-2 items-center mt-1">
                <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                  +967780236552
                </p>
                <div class="w-px h-3 bg-gray-300 dark:bg-gray-700"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  عدن
                </p>
              </div>

              <div class="flex gap-1 items-center mt-1">
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
          <div class="flex gap-3 p-3 rounded-lg transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
            <div
              class="overflow-hidden relative flex-shrink-0 w-12 h-12 rounded-full border border-gray-200 dark:border-gray-800">
              <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user"
                class="object-cover w-full h-full" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-800 truncate dark:text-white/90">
                محمد العبد (<span class="text-warning-500 dark:text-warning/90">فوكسي</span>)
              </h4>
              <div class="flex gap-2 items-center mt-1">
                <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                  +967712345678
                </p>
                <div class="w-px h-3 bg-gray-300 dark:bg-gray-700"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  صنعاء
                </p>
              </div>
              <!-- التقييم -->
              <div class="flex gap-1 items-center mt-1">
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
          <div class="flex gap-3 p-3 rounded-lg transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
            <div
              class="overflow-hidden relative flex-shrink-0 w-12 h-12 rounded-full border border-gray-200 dark:border-gray-800">
              <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user"
                class="object-cover w-full h-full" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-semibold text-gray-800 truncate dark:text-white/90">
                فاطمة قاسم (<span class="text-warning-500 dark:text-warning/90">باص</span>)
              </h4>
              <div class="flex gap-2 items-center mt-1">
                <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                  +967798765432
                </p>
                <div class="w-px h-3 bg-gray-300 dark:bg-gray-700"></div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  تعز
                </p>
              </div>
              <!-- التقييم -->
              <div class="flex gap-1 items-center mt-1">
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

  <livewire:request.index :dashboard-mode="true" />

@endsection

@section('script')
  <script defer src="{{ asset('tailadmin/build/bundle.js') }}?v=2.0.1"></script>
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

@endsection