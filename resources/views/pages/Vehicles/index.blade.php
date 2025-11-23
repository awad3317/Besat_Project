@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'إدارة المركبات')
@section('addButton')
  <div x-data="{ isModalOpen: false}">
    <button @click="isModalOpen = true"
      class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px]">
      إضافة مركبه
    </button>
    <div x-show="isModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
      style="display: none;">

      <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
      </div>

      <div @click.outside="isModalOpen = false"
        class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
        <form method="POST" action="{{ route('Vehicle.store') }}" enctype="multipart/form-data">
          @csrf
          <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
            إضافة مركبه جديدة
          </h4>
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
            <div class="col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                النوع</label>
              <input type="text" placeholder="مثال: فوكسي" name="type" required
                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            </div>

            <div class="col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                عدد الأشخاص
              </label>
              <input type="number" placeholder="مثال: 4" min="1" max="100" name="max_passengers" required
                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
            </div>

            <div class="col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                الوصف
              </label>
              <textarea placeholder="وصف المركبه" rows="4" name="description"
                class="hover:border-brand-500 dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs resize-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <div class="space-y-3">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                صورة المركبه
              </label>

              <div x-data="{ imagePreview: null }" class="relative">
                <label for="fileUpload_{{ Str::slug('image') }}"
                  class="cursor-pointer flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-800 hover:border-brand-500 dark:hover:border-brand-500 transition-colors duration-200 min-h-[120px] w-full">

                  <template x-if="imagePreview">
                    <div class="flex justify-center items-center w-full">
                      <img :src="imagePreview"
                        class="h-20 w-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                        alt="معاينة الصورة">
                    </div>
                  </template>

                  <template x-if="!imagePreview">
                    <div class="text-center">
                      <div class="mb-2 flex justify-center">
                        <div
                          class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                          <svg class="fill-current w-5 h-5" viewBox="0 0 29 28" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M14.5019 3.91699C14.2852 3.91699 14.0899 2.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z"
                              fill="" />
                          </svg>
                        </div>
                      </div>

                      <span class="text-xs text-brand-500 font-medium">
                        اضغط لرفع صورة المركبه
                      </span>
                    </div>
                  </template>
                </label>

                <input id="fileUpload_{{ Str::slug('image') }}" name="image" type="file" class="hidden" accept="image/*"
                  @change="imagePreview = URL.createObjectURL($event.target.files[0])" />
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end w-full gap-3 mt-6">
            <button @click="isModalOpen = false" type="button"
              class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
              إغلاق
            </button>
            <button type="submit" @click="saveFather()"
              class="flex justify-center w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
              إضافة المركبه
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <x-modals.success-modal />
  <x-modals.error-modal />

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
                      <img src="{{ url($vehicle->image) }}" alt="imge" />
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
                <div class="flex items-center justify-center">
                  <button onclick="window.location.href='{{ route('Vehicle.show', $vehicle->id) }}'"
                    class="flex mx-2 items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                   
                  </button>
                  <button onclick="window.location.href='{{ route('Vehicle.edit', $vehicle->id) }}'"
                    class="flex mx-2 items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
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