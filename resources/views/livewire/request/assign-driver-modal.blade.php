<div x-data="{ show: false }" x-on:open-modal.window="$event.detail == 'assign-driver-modal' ? show = true : null"
    x-on:close-modal.window="$event.detail == 'assign-driver-modal' ? show = false : null"
    x-on:close.stop="show = false" x-on:keydown.escape.window="show = false" x-show="show" class="relative z-99999"
    style="display: none;">
    {{-- Backdrop --}}
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-400/50 backdrop-blur-[32px]"></div>

    {{-- Modal Panel --}}
    <div class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto">
        <div x-show="show" x-transition @click.outside="show = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
            <div class="mb-6 flex items-center justify-between">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white/90">
                    تعيين سائق للطلب #{{ $selectedRequestId }}
                </h4>
                <button x-on:click="show = false" class="text-gray-400 hover:text-gray-500 transition">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- البحث --}}
            <div class="mb-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    البحث عن سائق
                </label>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="driverSearch" type="text"
                        placeholder="ابحث بالاسم أو رقم الهاتف..."
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- قائمة السائقين --}}
            <div>
                <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    نتائج البحث
                </label>
                <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($this->drivers as $driver)
                        <div
                            class="flex mb-1 items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-brand-100 hover:bg-brand-50/30 dark:border-gray-700 dark:hover:border-brand-500/30 dark:hover:bg-brand-900/10 transition group">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-12 w-12 overflow-hidden rounded-full border border-gray-100 dark:border-gray-700">
                                    <img src="{{ $driver->driver_image ? url($driver->driver_image) : asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                        alt="{{ $driver->name }}" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-gray-800 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition">
                                        {{ $driver->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $driver->phone }}</p>
                                </div>
                            </div>
                            <button wire:click="assignDriver({{ $driver->id }})" wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-md hover:shadow-lg">
                                <span wire:loading.remove wire:target="assignDriver({{ $driver->id }})">تعيين</span>
                                <span wire:loading wire:target="assignDriver({{ $driver->id }})">...</span>
                            </button>
                        </div>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-10 text-center rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div
                                class="h-12 w-12 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 font-medium">لا يوجد سائقين</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">جرب البحث باسم مختلف</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button x-on:click="show = false" type="button"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>