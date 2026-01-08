<div x-data="{ 
    isModalEditOpen: @if(session('openModalEdit')) true @else false @endif,
    imagePreview: null,
    isLoading: false 
}">
    <div x-show="isModalEditOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999"
        style="display: none;">

        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="isModalEditOpen = false">
        </div>

        <div class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
            @php
                $VehiclePricing = session('VehiclePricing');
            @endphp
            @if($VehiclePricing)
                <form method="POST" action="{{ route('vehiclePricing.update', $VehiclePricing->id) }}"
                    enctype="multipart/form-data" id="editVehicleForm" @submit="isLoading = true">
                    @csrf
                    @method('put')
                    <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
                        تعديل السعر
                    </h4>
                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                المسافة من (كم)
                            </label>
                            <div
                                class="h-11 w-full rounded-lg border border-gray-300 bg-yellow-50 px-4 py-2.5 text-sm text-gray-800 flex items-center dark:border-gray-600 dark:bg-yellow-900/20 dark:text-white">
                                {{ $VehiclePricing->min_distance_km }} كم
                                <span class="text-warning-500 dark:text-warning/90">(غير قابل للتعديل)</span>
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                المسافة إلى (كم) *
                            </label>
                            <input type="number" step="0.01" min="0" name="max_distance_km" requiredplaceholder="مثال: 100 "
                                value="{{ old('max_distance_km', $VehiclePricing->max_distance_km) }}"
                                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            </p>
                        </div>

                        <div class="col-span-1">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                السعر ({{ config('app.currency', 'ريال') }}) *
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" name="base_price" requiredplaceholder="مثال: 50.00"
                                    value="{{ old('base_price', $VehiclePricing->base_price) }}"
                                    class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end w-full gap-3 mt-6">
                        <button @click="isModalEditOpen = false" type="button"
                            class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                            إغلاق
                        </button>
                        <button type="submit" :disabled="isLoading"
                            class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all">
                            <!-- Loading Spinner -->
                            <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-text="isLoading ? 'جاري التحديث...' : 'تحديث السعر'"></span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>