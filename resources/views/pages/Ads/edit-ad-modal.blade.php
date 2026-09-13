<div x-data="{ isModalEditOpen: @if (session('openModalEdit')) true @else false @endif, isLoading: false }">

    <div x-show="isModalEditOpen" class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
        style="display: none;">
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
        </div>

        <div @click.outside="isModalEditOpen = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
            @php
                $Ad = session('Ad');
            @endphp
            @if ($Ad)
                <form method="POST" action="{{ route('ads.update', $Ad->id) }}" enctype="multipart/form-data"
                    @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90">
                        تعديل الإعلان
                    </h4>

                    <div class="grid grid-cols-1 gap-y-5 gap-x-6">

                        <div class="space-y-3" x-data="{ imagePreview: null }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                تحديث الصورة <span class="text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                            </label>

                            <div class="flex gap-4 items-center">
                                <!-- عرض الصورة القديمة -->
                                @if ($Ad && $Ad->image)
                                    <div class="flex-shrink-0">
                                        <div class="relative">
                                            <img src="{{ asset($Ad->image) }}"
                                                class="object-cover w-16 h-16 rounded-lg border border-gray-200 dark:border-gray-600"
                                                alt="الصورة الحالية">
                                            <span
                                                class="flex absolute -top-1 -right-1 justify-center items-center w-5 h-5 text-xs text-white bg-blue-500 rounded-full">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-center text-gray-500">الحالية</p>
                                    </div>
                                @endif

                                <!-- new file -->
                                <div class="flex-1">
                                    <div class="relative">
                                        <div @click="$refs.fileInput.click()"
                                            class="cursor-pointer flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-800 hover:border-brand-500 dark:hover:border-brand-500 transition-colors duration-200 min-h-[120px] w-full">

                                            <template x-if="imagePreview">
                                                <div class="flex flex-col justify-center items-center w-full">
                                                    <img :src="imagePreview"
                                                        class="object-cover mb-2 w-16 h-16 rounded-lg border border-gray-200 dark:border-gray-600"
                                                        alt="معاينة الصورة الجديدة">
                                                    <span class="text-xs font-medium text-green-600">صورة جديدة</span>
                                                </div>
                                            </template>

                                            <template x-if="!imagePreview">
                                                <div class="text-center">
                                                    <div class="flex justify-center mb-2">
                                                        <div
                                                            class="flex justify-center items-center w-10 h-10 text-gray-600 bg-gray-200 rounded-full dark:bg-gray-700 dark:text-gray-400">
                                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 29 28"
                                                                fill="none">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M14.5019 3.91699C14.2852 3.91699 14.0899 2.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z"
                                                                    fill="" />
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <span class="text-xs font-medium text-brand-500">
                                                        اضغط لرفع صورة جديدة
                                                    </span>
                                                    <p class="mt-1 text-xs text-gray-500">سيتم استبدال الصورة الحالية
                                                    </p>
                                                </div>
                                            </template>
                                        </div>

                                        <input x-ref="fileInput" id="edit_image" name="image" type="file"
                                            class="hidden" accept="image/*"
                                            @change="imagePreview = URL.createObjectURL($event.target.files[0])" />
                                        @error('image')
                                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <p class="mt-2 text-xs text-warning-500 dark:text-warning/90">
                                يدعم: jpeg, png, jpg, webp (الحد الأقصى: 2MB)
                            </p>
                        </div>

                        <div>
                            {{-- Toggle Switch --}}
                            <div x-data="{ isActive: {{ $Ad->is_active ? 'true' : 'false' }} }">
                                <div @click="isActive = !isActive"
                                    class="flex gap-3 items-center text-sm font-medium text-gray-700 cursor-pointer select-none dark:text-gray-400">
                                    <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                                    <div class="relative">
                                        <div class="block w-11 h-6 rounded-full transition-colors duration-300"
                                            :class="isActive ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                        </div>
                                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-300 ease-in-out"
                                            :class="{ 'translate-x-full': isActive }">
                                        </div>
                                    </div>
                                    <span>تفعيل الإعلان</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex gap-3 justify-end items-center mt-6 w-full">
                        <button @click="isModalEditOpen = false" type="button"
                            class="flex justify-center px-4 py-3 w-full text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:border-brand-500 sm:w-auto">
                            إغلاق
                        </button>
                        <button type="submit" :disabled="isLoading"
                            class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg transition-all hover:bg-brand-600 bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed">
                            <!-- Loading Spinner -->
                            <svg x-show="isLoading" class="w-5 h-5 text-white animate-spin"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-text="isLoading ? 'جاري التعديل...' : 'تعديل الإعلان'"></span>
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>
