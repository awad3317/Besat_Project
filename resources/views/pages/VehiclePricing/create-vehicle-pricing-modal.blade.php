<div x-data="{ isCreateModalOpen: false }">
    <!-- زر فتح المودال -->
    <button @click="isCreateModalOpen = true"
        class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px]">
        إضافة سعر جديد
    </button>
    <div x-show="isCreateModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
        style="display: none;">

        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
        </div>

        <div @click.outside="isCreateModalOpen = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
            <form method="POST" action="{{ route('vehiclePricing.store') }}" enctype="multipart/form-data">
                @csrf
                <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
                    إضافة سعر جديد للمركبة
                </h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            المسافة من (كم) *
                        </label>
                        <input type="number" step="0.01" min="0" name="min_distance_km" required placeholder="مثال: 0"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            المسافة إلى (كم) *
                        </label>
                        <input type="number" step="0.01" min="0" name="max_distance_km" required
                            placeholder="مثال: 100 "
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        </p>
                    </div>

                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            السعر ({{ config('app.currency', 'ريال') }}) *
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" name="base_price" required
                                placeholder="مثال: 50.00"
                                class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="isCreateModalOpen = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                        إغلاق
                    </button>
                    <button type="submit"
                        class="flex justify-center hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
                        إضافة السعر
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>