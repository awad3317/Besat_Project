@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'إدارة السائقين')
@section('content')
    <div class="space-y-5 sm:space-y-6">
              <div
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
              >
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                  <h3
                    class="text-base font-medium text-gray-800 dark:text-white/90"
                  >
                    السائقين
                  </h3>
                </div>
                <div
                  class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6"
                >
                  <!-- ====== Table Six Start -->
                  <div
  class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
>
  <div class="max-w-full overflow-x-auto">
    <table class="min-w-full">
      <!-- table header start -->
      <thead>
        <tr class="border-b border-gray-100 dark:border-gray-800">
          <th class="px-5 py-3 sm:px-6">
            <div class="flex items-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                الأسم
              </p>
            </div>
          </th>
          <th class="px-5 py-3 sm:px-6">
            <div class="flex items-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                رقم الواتساب
              </p>
            </div>
          </th>
          <th class="px-5 py-3 sm:px-6">
            <div class="flex items-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                نوعه
              </p>
            </div>
          </th>
          <th class="px-5 py-3 sm:px-6">
            <div class="flex items-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                حالته
              </p>
            </div>
          </th>
          <th class="px-5 py-3 sm:px-6">
            <div class="flex items-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                الطلبات
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
              <div class="flex items-center gap-3">
                <div>
                  <span
                    class="block font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    احمد شرجبي
                  </span>
                  <span
                    class="block text-gray-500 text-theme-xs dark:text-gray-400"
                  >
                    +967780236552
                  </span>
                </div>
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
              <p
                class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500"
              >
                مستخدم عادي
              </p>
            </div>
          </td>
          <td class="px-5 py-4 sm:px-6">
            <div class="flex items-center">
              <p
                class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500"
              >
                نشظ
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
</div>
<!-- ====== Table Six End -->
                </div>
              </div>
            </div>
@endsection
