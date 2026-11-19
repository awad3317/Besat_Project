{{-- نافذة إلغاء الرحلة مع إدخال السبب --}}
<div x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail == 'cancel-request-modal' || $event.detail?.[0] == 'cancel-request-modal') { show = true; }"
    x-on:close-modal.window="if ($event.detail == 'cancel-request-modal' || $event.detail?.[0] == 'cancel-request-modal') { show = false; }"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
    style="display: none;">
    <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]" @click="show = false"></div>

    <div @click.outside="show = false"
        class="relative z-10 p-6 w-full max-w-md bg-white rounded-3xl border border-gray-200 shadow-theme-lg dark:bg-gray-900 dark:border-gray-800"
        dir="rtl">
        <div class="text-center">
            <div class="flex justify-center items-center mx-auto mb-4 w-14 h-14 rounded-full bg-error-50 text-error-500 dark:bg-error-500/15">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">
                تأكيد إلغاء الرحلة #{{ $cancellingRequestId }}
            </h4>
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">
                يرجى توضيح سبب إلغاء هذه الرحلة لحفظه في سجل العمليات (اللوق).
            </p>
        </div>

        <form wire:submit.prevent="cancelRequest" class="space-y-4">
            <div>
                <label class="block mb-1.5 text-xs font-medium text-right text-gray-700 dark:text-gray-300">
                    سبب الإلغاء <span class="text-error-500">*</span>
                </label>
                <textarea wire:model="cancellationReason" rows="3"
                    placeholder="اكتب سبب إلغاء الرحلة هنا..."
                    class="px-3 py-2 w-full text-sm text-right bg-transparent rounded-xl border border-gray-300 dark:border-gray-700 dark:text-white focus:border-error-500 focus:ring-1 focus:ring-error-500"></textarea>
                @error('cancellationReason')
                    <span class="block mt-1 text-xs text-right text-error-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-3 justify-center pt-2">
                <button type="button" @click="show = false"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                    تراجع
                </button>
                <button type="submit" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-xl transition bg-error-600 hover:bg-error-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="cancelRequest">تأكيد الإلغاء</span>
                    <span wire:loading wire:target="cancelRequest">جاري الإلغاء...</span>
                </button>
            </div>
        </form>
    </div>
</div>
