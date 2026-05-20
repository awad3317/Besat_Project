<div x-data="{ isModalEditOpen: @if(session('openModalEdit')) true @else false @endif, isLoading: false }">

    <div x-show="isModalEditOpen"
        class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999" style="display: none;" x-transition>
        
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="isModalEditOpen = false">
        </div>

        <div @click.outside="isModalEditOpen = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10 z-10">
            
            @php
                // جلب بيانات البنك الممررة عبر السيشن من الكنترولر عند الضغط على تعديل
                $bank = session('Bank');
            @endphp
            
            @if($bank)
                <form method="POST" action="{{ route('Bank.update', $bank->id) }}" enctype="multipart/form-data" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    
                    <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90 text-right">
                        تعديل بيانات الحساب البنكي
                    </h4>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 text-right">

                        <div class="sm:col-span-2">
                            <label for="edit_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                اسم البنك <span class="text-xs text-warning-500">*</span>
                            </label>
                            <input type="text" id="edit_name" name="name" required value="{{ old('name', $bank->name) }}"
                                placeholder="مثال: بنك الكريمي الإسلامي"
                                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white text-right">
                            @error('name') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="edit_account_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                اسم صاحب الحساب <span class="text-xs text-gray-400">(اختياري)</span>
                            </label>
                            <input type="text" id="edit_account_name" name="account_name" value="{{ old('account_name', $bank->account_name) }}"
                                placeholder="مثال: شركة بساط للتكنولوجيا"
                                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white text-right">
                            @error('account_name') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-1">
                            <label for="edit_account_number" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                رقم الحساب أو الآيبان <span class="text-xs text-warning-500">*</span>
                            </label>
                            <input type="text" id="edit_account_number" name="account_number" required value="{{ old('account_number', $bank->account_number) }}"
                                placeholder="700xxxxxx أو IBAN"
                                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white text-left font-mono" dir="ltr">
                            @error('account_number') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="edit_logo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                شعار / أيقونة البنك <span class="text-xs text-gray-400">(اتركه فارغاً للاحتفاظ بالشعار الحالي)</span>
                            </label>
                            
                            @if($bank->logo)
                                <div class="flex items-center gap-3 mb-3 p-2 border rounded-xl dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 w-fit">
                                    <img src="{{ asset($bank->logo) }}" class="h-12 w-12 object-contain rounded-lg border bg-white" alt="Current Logo">
                                    <span class="text-xs text-gray-400">الشعار الحالي النشط</span>
                                </div>
                            @endif

                            <input type="file" id="edit_logo" name="logo" accept="image/*"
                                class="hover:border-brand-500 dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white file:ml-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            @error('logo') <p class="text-xs text-error-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2 mt-2">
                            <div x-data="{ isActive: {{ $bank->is_active ? 'true' : 'false' }} }">
                                <div @click="isActive = !isActive"
                                    class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400 justify-start flex-row-reverse w-fit">
                                    <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                                    <span class="mr-3">تنشيط واستقبال الإيداعات على هذا الحساب</span>
                                    <div class="relative">
                                        <div class="block h-6 w-11 rounded-full transition-colors duration-300"
                                            :class="isActive ? 'bg-success-500' : 'bg-gray-200 dark:bg-gray-700'">
                                        </div>
                                        <div class="shadow-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white transition-transform duration-300 ease-in-out"
                                            :class="{ 'translate-x-full': isActive }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end w-full gap-3 mt-6">
                        <button @click="isModalEditOpen = false" type="button"
                            class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                            إغلاق
                        </button>
                        <button type="submit" :disabled="isLoading"
                            class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all sm:w-auto">
                            
                            <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" style="display: none;"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isLoading ? 'جاري الحفظ والرفع...' : 'حفظ التعديلات'"></span>
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>